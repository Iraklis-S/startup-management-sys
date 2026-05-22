<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use League\Csv\Reader;

class ImportDataset extends Command
{
    //  php artisan import:dataset storage/app/dataset --fresh
    protected $signature = 'import:dataset
                            {path=storage/app/dataset : Path to folder with CSV files}
                            {--fresh : Truncate dataset tables before import}';

    protected $description = 'Forgiving import of Crunchbase CSV dataset into the project schema';

    private const BATCH_SIZE = 1000;

    private array $objectIdMap = [];
    private array $roundIdMap = [];
    private array $personIdMap = [];
    private array $permalinkIdMap = [];
    private array $tableColumns = [];
    private array $tempCsvFiles = [];
    private string $originalSqlMode = '';

    private array $requiredFiles = [
        'objects.csv',
        'people.csv',
        'funding_rounds.csv',
        'investments.csv',
        'funds.csv',
        'acquisitions.csv',
        'ipos.csv',
        'milestones.csv',
        'offices.csv',
        'relationships.csv',
        'degrees.csv',
    ];

    // main entry point when artisan command runs
    //this method calls all of the other helper methods that will import into the tables 
    public function handle(): int
    {
        //gets an usable file-system path
        $path = $this->resolvePath($this->argument('path'));

        // if folder doesn't exist ,stop command
        if (!is_dir($path)) {
            $this->error("Folder not found: {$path}");
            return self::FAILURE;
        }

        // builds a list of missing files ,if any are missing
        $missing = [];
        foreach ($this->requiredFiles as $file) {
            if (!file_exists($path . DIRECTORY_SEPARATOR . $file)) {
                $missing[] = $file;
            }
        }

        // if there is at least 1 missing file , the command aborts
        if (!empty($missing)) {
            $this->error('Missing CSV files: ' . implode(', ', $missing));
            return self::FAILURE;
        }

        DB::disableQueryLog();


        // this try/finally block ensures that the finally block runs 
        // this allows for the session state to be returned to normal 
        // cleaning temporary csv and restoring constraints
        try {
            $this->disableSessionConstraints();

            if ($this->option('fresh')) {
                $this->warn('Fresh import selected. Truncating dataset tables...');
                $this->truncateDatasetTables();
            }

            $this->info("Import path: {$path}");

            $this->importKompanite($path . '/objects.csv');
            $this->importPersonat($path . '/people.csv');
            $this->importRaundet($path . '/funding_rounds.csv');
            $this->importInvestimet($path . '/investments.csv');
            $this->importFondet($path . '/funds.csv');
            $this->importBlerjet($path . '/acquisitions.csv');
            $this->importIpos($path . '/ipos.csv');
            $this->importArritjet($path . '/milestones.csv');
            $this->importZyrat($path . '/offices.csv');
            $this->importMarredheniet($path . '/relationships.csv');
            $this->importEdukimet($path . '/degrees.csv');
        } finally {
            $this->cleanupTempCsvFiles();
            $this->restoreSessionConstraints();
        }
        // provides a success message in a new line
        $this->newLine();
        $this->info('Import completed successfully.');

        return self::SUCCESS;
    }


    // method to disable constraints , so that there are no trouble when importing the dataset from CSV

    private function disableSessionConstraints(): void
    {
        // get the current sql mode
        $result = DB::selectOne('SELECT @@SESSION.sql_mode AS mode');
        // store that value in the Class attribute  originalSqlMode
        $this->originalSqlMode = $result->mode ?? '';
        // clear all sql_modes for this session
        DB::statement("SET SESSION sql_mode=''");
        // disable foreign key checks ,so that import works without trouble 
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        //disable unique constraint checks
        DB::statement("SET UNIQUE_CHECKS=0");
    }

    // method to restore the session to normal after import is finished
    // allowing constraints to be re-instated and to work as supposed in the normal app functions
    private function restoreSessionConstraints(): void
    {
        try {
            DB::statement("SET UNIQUE_CHECKS=1");
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
            DB::statement("SET SESSION sql_mode=" . DB::getPdo()->quote($this->originalSqlMode));
        } catch (\Throwable $e) {
        }
    }

    // helper method that is called when --fresh flag is used with this command
    //to clear all data existing in tables
    private function truncateDatasetTables(): void
    {
        $tables = [
            'edukimet',
            'marredheniet',
            'zyrat',
            'arritjet',
            'ipos',
            'blerjet',
            'fondet',
            'investimet',
            'raundet_financimit',
            'personat',
            'kompanite',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
    }


    // method to import the data into the kompanite table
    private function importKompanite(string $file): void
    {
        // print the string in a new line ,to indicate the command started working on this task
        $this->newLine();
        $this->info('1/11 Importing objects.csv -> kompanite');

        // read csv file
        $csv = $this->openCsv($file);

        $parentLinks = [];
        // counts number of inserted rows  ,its incremented continuously per row 
        $inserted = 0;
        // get the next id where data can be inserted in the table
        $nextId = $this->nextIdForTable('kompanite');
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            // generates an id for the row
            $csvId = $this->rowKey($row, 'obj');

            // assign entity_type a  value of "unknown" if it's null
            $entityType = $this->cleanString($row['entity_type'] ?? null) ?? 'unknown';
            // if entityType is person , we skip that , as we don't want in our logic to insert people into the companies
            if (strtolower($entityType) === 'person') {
                continue;
            }
            // map the type to be startup or other
            $companyType = $this->mapCompanyType($entityType, $row);
            // get a name for the company ,based in one of the fields from the CSV or a fallback value
            $name = $this->companyName($row, $csvId, $entityType);
            // get the normalized_name or generate a slug 
            $normalizedName = $this->cleanString($row['normalized_name'] ?? null) ?? $this->slug($name);
            //get the permalink or generate one
            $permalink = $this->uniquePermalink($row['permalink'] ?? null, $csvId, $entityType);
            //   get the category Code or put it as unknown
            $categoryCode = $this->cleanString($row['category_code'] ?? null) ?? 'unknown';
            //    get the value from the status field, if its null , put it as 'operating'
            $status = $this->mapStatus($row['status'] ?? null) ?? 'operating';
            //get the founding date, if it's not available ,insert the date as 1970
            $foundedAt = $this->date($row['founded_at'] ?? null, '1970-01-01');

            // assigns the current value of the $nextId to $assignedId then increments $nextId
            $assignedId = $nextId++;
            // prepares the id column to take the value of the assignedId
            $data = ['id' => $assignedId];
            // builds the data array
            $this->set($data, 'kompanite', ['company_type', 'companytype'], $companyType);
            $this->set($data, 'kompanite', ['name'], $name);
            $this->set($data, 'kompanite', ['normalized_name', 'normalizedname'], $normalizedName);
            $this->set($data, 'kompanite', ['permalink'], $permalink);
            $this->set($data, 'kompanite', ['category_code', 'categorycode'], $categoryCode);
            $this->set($data, 'kompanite', ['status'], $status);
            $this->set($data, 'kompanite', ['founded_at', 'foundedat'], $foundedAt);

            if ($this->hasAnyColumn('kompanite', ['verification_status'])) {
                $this->set($data, 'kompanite', ['verification_status'], 'verified');
            }

            if ($this->hasAnyColumn('kompanite', ['verified_by'])) {
                $this->set($data, 'kompanite', ['verified_by'], 0);
            }

            $this->applyTimestamps('kompanite', $data);
            $this->applyVerificationFields('kompanite', $data);

            $this->objectIdMap[$csvId] = $assignedId;
            $this->permalinkIdMap[$permalink] = $assignedId;

            $parentCsvId = $this->cleanString($row['parent_id'] ?? null);
            if ($parentCsvId !== null) {
                $parentLinks[] = [$csvId, $parentCsvId];
            }

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('kompanite', $batch);
        }

        $this->flushInsertBatch('kompanite', $batch, true);

        $bar->finish();
        $this->newLine();

        $parentCol = $this->col('kompanite', ['parent_id', 'parentid']);
        if ($parentCol) {
            foreach ($parentLinks as [$childCsvId, $parentCsvId]) {
                $childId = $this->objectIdMap[$childCsvId] ?? null;
                $parentId = $this->objectIdMap[$parentCsvId] ?? 0;

                if ($childId) {
                    DB::table('kompanite')->where('id', $childId)->update([$parentCol => $parentId]);
                }
            }
        }

        $this->line("Inserted kompanite: {$inserted}");
    }

    private function importPersonat(string $file): void
    {
        $this->newLine();
        $this->info('2/11 Importing people.csv -> personat');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $updated = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        // We'll insert person rows and keep an in-memory map from CSV person object_id -> personat.id
        $nextId = $this->nextIdForTable('personat');

        foreach ($csv->getRecords() as $row) {
            $csvObjectId = $this->cleanString($row['object_id'] ?? null) ?? '0';

            $affiliationName = $this->cleanString($row['affiliation_name'] ?? null);
            if ($affiliationName !== null && strtolower($affiliationName) === 'unaffiliated') {
                $bar->advance();
                continue;
            }

            $assignedId = $nextId++;
            $data = ['id' => $assignedId];
            // Do not set company_id here; relationships.csv will link persons to companies
            $this->set($data, 'personat', ['first_name', 'firstname'], $this->cleanString($row['first_name'] ?? null) ?? 'Unknown');
            $this->set($data, 'personat', ['last_name', 'lastname'], $this->cleanString($row['last_name'] ?? null) ?? "Person {$csvObjectId}");
            $this->set($data, 'personat', ['birthplace'], $this->cleanString($row['birthplace'] ?? null) ?? 'Unknown');
            $this->set($data, 'personat', ['affiliation_name', 'affiliationname'], $affiliationName ?? 'Unknown');
            $this->applyTimestamps('personat', $data);
            $this->applyVerificationFields('personat', $data);

            $this->personIdMap[$csvObjectId] = $assignedId;

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('personat', $batch);
        }

        $this->flushInsertBatch('personat', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted personat: {$inserted}, updated existing: {$updated}");
    }

    private function importRaundet(string $file): void
    {
        $this->newLine();
        $this->info('3/11 Importing funding_rounds.csv -> raundet_financimit');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $nextId = $this->nextIdForTable('raundet_financimit');
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRoundPk = $this->rowKey($row, 'round');
            $csvObjectId = $this->cleanString($row['object_id'] ?? null) ?? '0';
            $resolvedObjectId = $this->objectIdMap[$csvObjectId] ?? 0;

            $assignedId = $nextId++;
            $data = ['id' => $assignedId];
            $this->set($data, 'raundet_financimit', ['funding_round_id', 'fundingroundid'], $this->cleanString($row['funding_round_id'] ?? null) ?? "funding-round-{$csvRoundPk}");
            $this->set($data, 'raundet_financimit', ['company_id', 'companyid'], $resolvedObjectId);
            $this->set($data, 'raundet_financimit', ['funded_at', 'fundedat'], $this->date($row['funded_at'] ?? null, '1970-01-01'));
            $this->set($data, 'raundet_financimit', ['funding_round_type', 'fundingroundtype'], $this->cleanString($row['funding_round_type'] ?? null) ?? 'unknown');
            $this->set($data, 'raundet_financimit', ['funding_round_code', 'fundingroundcode'], $this->cleanString($row['funding_round_code'] ?? null) ?? 'unknown');
            $this->set($data, 'raundet_financimit', ['raised_amount_usd', 'raisedamountusd'], $this->decimal($row['raised_amount_usd'] ?? null, 0));
            $this->set($data, 'raundet_financimit', ['raised_amount', 'raisedamount'], $this->decimal($row['raised_amount'] ?? null, 0));
            $this->set($data, 'raundet_financimit', ['raised_currency_code', 'raisedcurrencycode'], $this->cleanString($row['raised_currency_code'] ?? null) ?? 'USD');
            $this->set($data, 'raundet_financimit', ['pre_money_valuation_usd', 'premoneyvaluationusd'], $this->decimal($row['pre_money_valuation_usd'] ?? null, 0));
            $this->applyTimestamps('raundet_financimit', $data);
            $this->applyVerificationFields('raundet_financimit', $data);

            $this->roundIdMap[$csvRoundPk] = $assignedId;
            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('raundet_financimit', $batch);
        }

        $this->flushInsertBatch('raundet_financimit', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted raundet_financimit: {$inserted}");
    }

    private function importInvestimet(string $file): void
    {
        $this->newLine();
        $this->info('4/11 Importing investments.csv -> investimet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $roundCsvId = $this->cleanString($row['funding_round_id'] ?? null) ?? '0';
            $fundedCsvId = $this->cleanString($row['funded_object_id'] ?? null) ?? '0';
            $investorCsvId = $this->cleanString($row['investor_object_id'] ?? null) ?? '0';

            $data = [];
            $this->set($data, 'investimet', ['funding_round_id', 'fundingroundid'], $this->roundIdMap[$roundCsvId] ?? 0);
            $this->set($data, 'investimet', ['funded_company_id', 'fundedcompanyid'], $this->objectIdMap[$fundedCsvId] ?? 0);
            $this->set($data, 'investimet', ['investor_company_id', 'investorcompanyid'], $this->objectIdMap[$investorCsvId] ?? 0);

            $this->applyTimestamps(
                'investimet',
                $data,
                $this->datetime($row['created_at'] ?? null, '1970-01-01 00:00:00'),
                $this->datetime($row['updated_at'] ?? null, '1970-01-01 00:00:00')
            );
            $this->applyVerificationFields('investimet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('investimet', $batch);
        }

        $this->flushInsertBatch('investimet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted investimet: {$inserted}");
    }

    private function importFondet(string $file): void
    {
        $this->newLine();
        $this->info('5/11 Importing funds.csv -> fondet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'fund');
            $csvObjectId = $this->cleanString($row['object_id'] ?? null) ?? '0';

            $data = [];
            $this->set($data, 'fondet', ['fund_id', 'fundid'], $this->cleanString($row['fund_id'] ?? null) ?? "fund-{$csvRowId}");
            $this->set($data, 'fondet', ['company_id', 'companyid'], $this->objectIdMap[$csvObjectId] ?? 0);
            $this->set($data, 'fondet', ['name'], $this->cleanString($row['name'] ?? null) ?? "Unknown Fund {$csvRowId}");
            $this->set($data, 'fondet', ['funded_at', 'fundedat'], $this->date($row['funded_at'] ?? null, '1970-01-01'));
            $this->set($data, 'fondet', ['raised_amount', 'raisedamount'], $this->decimal($row['raised_amount'] ?? null, 0));
            $this->set($data, 'fondet', ['raised_currency_code', 'raisedcurrencycode'], $this->cleanString($row['raised_currency_code'] ?? null) ?? 'USD');
            $this->set($data, 'fondet', ['source_url', 'sourceurl'], $this->cleanString($row['source_url'] ?? null) ?? $this->placeholderUrl('funds', $csvRowId));
            $this->set($data, 'fondet', ['source_description', 'sourcedescription'], $this->cleanString($row['source_description'] ?? null) ?? "No source description for fund {$csvRowId}");

            $this->applyTimestamps(
                'fondet',
                $data,
                $this->datetime($row['created_at'] ?? null, '1970-01-01 00:00:00'),
                '1970-01-01 00:00:00'
            );
            $this->applyVerificationFields('fondet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('fondet', $batch);
        }

        $this->flushInsertBatch('fondet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted fondet: {$inserted}");
    }

    private function importBlerjet(string $file): void
    {
        $this->newLine();
        $this->info('6/11 Importing acquisitions.csv -> blerjet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'acq');

            $data = [];
            $this->set($data, 'blerjet', ['acquisition_id', 'acquisitionid'], $this->cleanString($row['acquisition_id'] ?? null) ?? "acquisition-{$csvRowId}");
            $this->set($data, 'blerjet', ['acquiring_company_id', 'acquiringcompanyid'], $this->objectIdMap[$this->cleanString($row['acquiring_object_id'] ?? null) ?? '0'] ?? 0);
            $this->set($data, 'blerjet', ['acquired_company_id', 'acquiredcompanyid'], $this->objectIdMap[$this->cleanString($row['acquired_object_id'] ?? null) ?? '0'] ?? 0);
            $this->set($data, 'blerjet', ['term_code', 'termcode'], $this->cleanString($row['term_code'] ?? null) ?? 'unknown');
            $this->set($data, 'blerjet', ['price_amount', 'priceamount'], $this->decimal($row['price_amount'] ?? null, 0));
            $this->set($data, 'blerjet', ['price_currency_code', 'pricecurrencycode'], $this->cleanString($row['price_currency_code'] ?? null) ?? 'USD');
            $this->set($data, 'blerjet', ['acquired_at', 'acquiredat'], $this->date($row['acquired_at'] ?? null, '1970-01-01'));
            $this->set($data, 'blerjet', ['source_url', 'sourceurl'], $this->cleanString($row['source_url'] ?? null) ?? $this->placeholderUrl('acquisitions', $csvRowId));
            $this->set($data, 'blerjet', ['source_description', 'sourcedescription'], $this->cleanString($row['source_description'] ?? null) ?? "No source description for acquisition {$csvRowId}");

            $this->applyTimestamps('blerjet', $data);
            $this->applyVerificationFields('blerjet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('blerjet', $batch);
        }

        $this->flushInsertBatch('blerjet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted blerjet: {$inserted}");
    }

    private function importIpos(string $file): void
    {
        $this->newLine();
        $this->info('7/11 Importing ipos.csv -> ipos');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'ipo');

            $data = [];
            $this->set($data, 'ipos', ['company_id', 'companyid'], $this->objectIdMap[$this->cleanString($row['object_id'] ?? null) ?? '0'] ?? 0);
            $this->set($data, 'ipos', ['valuation_amount', 'valuationamount'], $this->decimal($row['valuation_amount'] ?? null, 0));
            $this->set($data, 'ipos', ['valuation_currency_code', 'valuationcurrencycode'], $this->cleanString($row['valuation_currency_code'] ?? null) ?? 'USD');
            $this->set($data, 'ipos', ['raised_amount', 'raisedamount'], $this->decimal($row['raised_amount'] ?? null, 0));
            $this->set($data, 'ipos', ['raised_currency_code', 'raisedcurrencycode'], $this->cleanString($row['raised_currency_code'] ?? null) ?? 'USD');
            $this->set($data, 'ipos', ['public_at', 'publicat'], $this->date($row['public_at'] ?? null, '1970-01-01'));
            $this->set($data, 'ipos', ['stock_symbol', 'stocksymbol'], $this->cleanString($row['stock_symbol'] ?? null) ?? "UNK{$csvRowId}");
            $this->set($data, 'ipos', ['source_url', 'sourceurl'], $this->cleanString($row['source_url'] ?? null) ?? $this->placeholderUrl('ipos', $csvRowId));

            $this->applyTimestamps('ipos', $data);
            $this->applyVerificationFields('ipos', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('ipos', $batch);
        }

        $this->flushInsertBatch('ipos', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted ipos: {$inserted}");
    }

    private function importArritjet(string $file): void
    {
        $this->newLine();
        $this->info('8/11 Importing milestones.csv -> arritjet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'milestone');

            $data = [];
            $this->set($data, 'arritjet', ['company_id', 'companyid'], $this->objectIdMap[$this->cleanString($row['object_id'] ?? null) ?? '0'] ?? 0);
            $this->set($data, 'arritjet', ['milestone_at', 'milestoneat'], $this->date($row['milestone_at'] ?? null, '1970-01-01'));
            $this->set($data, 'arritjet', ['milestone_code', 'milestonecode'], $this->cleanString($row['milestone_code'] ?? null) ?? 'unknown');
            $this->set($data, 'arritjet', ['source_url', 'sourceurl'], $this->cleanString($row['source_url'] ?? null) ?? $this->placeholderUrl('milestones', $csvRowId));
            $this->set($data, 'arritjet', ['source_description', 'sourcedescription'], $this->cleanString($row['source_description'] ?? null) ?? $this->cleanString($row['description'] ?? null) ?? "No milestone description {$csvRowId}");

            $this->applyTimestamps('arritjet', $data);
            $this->applyVerificationFields('arritjet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('arritjet', $batch);
        }

        $this->flushInsertBatch('arritjet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted arritjet: {$inserted}");
    }

    private function importZyrat(string $file): void
    {
        $this->newLine();
        $this->info('9/11 Importing offices.csv -> zyrat');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'office');

            $data = [];
            $this->set($data, 'zyrat', ['company_id', 'companyid'], $this->objectIdMap[$this->cleanString($row['object_id'] ?? null) ?? '0'] ?? 0);
            $this->set($data, 'zyrat', ['office_id', 'officeid'], $this->cleanString($row['office_id'] ?? null) ?? "office-{$csvRowId}");
            $this->set($data, 'zyrat', ['description'], $this->cleanString($row['description'] ?? null) ?? "No office description {$csvRowId}");
            $this->set($data, 'zyrat', ['region'], $this->cleanString($row['region'] ?? null) ?? 'Unknown');
            $this->set($data, 'zyrat', ['city'], $this->cleanString($row['city'] ?? null) ?? 'Unknown');
            $this->set($data, 'zyrat', ['zip_code', 'zipcode'], $this->cleanString($row['zip_code'] ?? null) ?? '00000');
            $this->set($data, 'zyrat', ['latitude'], $this->decimal($row['latitude'] ?? null, 0));
            $this->set($data, 'zyrat', ['longitude'], $this->decimal($row['longitude'] ?? null, 0));

            $this->applyTimestamps('zyrat', $data);
            $this->applyVerificationFields('zyrat', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('zyrat', $batch);
        }

        $this->flushInsertBatch('zyrat', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted zyrat: {$inserted}");
    }

    private function importMarredheniet(string $file): void
    {
        $this->newLine();
        $this->info('10/11 Importing relationships.csv -> marredheniet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'rel');

            $data = [];
            $personCsvId = $this->cleanString($row['person_object_id'] ?? null) ?? '0';
            $relationshipCsvId = $this->cleanString($row['relationship_object_id'] ?? null) ?? '0';

            $this->set($data, 'marredheniet', ['person_id', 'personid'], $this->personIdMap[$personCsvId] ?? 0);
            $this->set($data, 'marredheniet', ['company_id', 'companyid'], $this->objectIdMap[$relationshipCsvId] ?? 0);
            $this->set($data, 'marredheniet', ['start_at', 'startat'], $this->date($row['start_at'] ?? null, null));
            $this->set($data, 'marredheniet', ['end_at', 'endat'], $this->date($row['end_at'] ?? null, null));

            // Validate date logic: if end_at < start_at, nullify end_at to preserve the row
            $startCol = $this->col('marredheniet', ['start_at', 'startat']);
            $endCol = $this->col('marredheniet', ['end_at', 'endat']);
            if ($startCol && $endCol && isset($data[$startCol]) && isset($data[$endCol])) {
                if ($data[$startCol] !== null && $data[$endCol] !== null && $data[$endCol] < $data[$startCol]) {
                    $data[$endCol] = null;
                }
            }

            $this->set($data, 'marredheniet', ['is_past', 'ispast'], $this->boolValue($row['is_past'] ?? null, false));
            $this->set($data, 'marredheniet', ['sequence'], $this->intValue($row['sequence'] ?? null, 0));
            $this->set($data, 'marredheniet', ['title'], $this->cleanString($row['title'] ?? null) ?? "Unknown Role {$csvRowId}");

            $this->applyTimestamps('marredheniet', $data);
            $this->applyVerificationFields('marredheniet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('marredheniet', $batch);
        }

        $this->flushInsertBatch('marredheniet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted marredheniet: {$inserted}");
    }

    private function importEdukimet(string $file): void
    {
        $this->newLine();
        $this->info('11/11 Importing degrees.csv -> edukimet');

        $csv = $this->openCsv($file);
        $inserted = 0;
        $batch = [];
        $bar = $this->output->createProgressBar();

        foreach ($csv->getRecords() as $row) {
            $csvRowId = $this->rowKey($row, 'degree');

            $data = [];
            $degreePersonCsvId = $this->cleanString($row['object_id'] ?? null) ?? '0';
            $this->set($data, 'edukimet', ['person_id', 'personid'], $this->personIdMap[$degreePersonCsvId] ?? 0);
            $this->set($data, 'edukimet', ['degree_type', 'degreetype'], $this->cleanString($row['degree_type'] ?? null) ?? 'Unknown');
            $this->set($data, 'edukimet', ['subject'], $this->cleanString($row['subject'] ?? null) ?? "Unknown Subject {$csvRowId}");
            $this->set($data, 'edukimet', ['institution'], $this->cleanString($row['institution'] ?? null) ?? "Unknown Institution {$csvRowId}");
            $this->set($data, 'edukimet', ['graduated_at', 'graduatedat'], $this->date($row['graduated_at'] ?? null, '1970-01-01'));

            $this->applyTimestamps('edukimet', $data);
            $this->applyVerificationFields('edukimet', $data);

            $batch[] = $data;
            $inserted++;
            $bar->advance();

            $this->flushInsertBatch('edukimet', $batch);
        }

        $this->flushInsertBatch('edukimet', $batch, true);

        $bar->finish();
        $this->newLine();
        $this->line("Inserted edukimet: {$inserted}");
    }

    private function resolvePath(string $inputPath): string
    {
        if (str_starts_with($inputPath, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\\\/', $inputPath)) {
            return rtrim($inputPath, DIRECTORY_SEPARATOR);
        }

        return rtrim(base_path(trim($inputPath, '/\\')), DIRECTORY_SEPARATOR);
    }

    private function openCsv(string $file): Reader
    {
        if (!file_exists($file)) {
            throw new \RuntimeException("CSV file not found: {$file}");
        }

        if (!is_file($file)) {
            throw new \RuntimeException("Path is not a file: {$file}");
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'csv_import_');

        if ($tmpFile === false) {
            throw new \RuntimeException("Could not create temp file for: {$file}");
        }

        if (!@copy($file, $tmpFile)) {
            $error = error_get_last();
            @unlink($tmpFile);

            throw new \RuntimeException(
                "Could not copy CSV to temp file: {$file}" .
                (!empty($error['message']) ? " | {$error['message']}" : '')
            );
        }

        $this->tempCsvFiles[] = $tmpFile;

        $spl = new \SplFileObject($tmpFile, 'r');
        $csv = Reader::createFromFileObject($spl);
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function cleanupTempCsvFiles(): void
    {
        foreach ($this->tempCsvFiles as $tmpFile) {
            if (is_string($tmpFile) && $tmpFile !== '' && file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }

        $this->tempCsvFiles = [];
    }

    private function columns(string $table): array
    {
        if (!isset($this->tableColumns[$table])) {
            $cols = Schema::getColumnListing($table);
            $mapped = [];

            foreach ($cols as $col) {
                $mapped[strtolower($col)] = $col;
            }

            $this->tableColumns[$table] = $mapped;
        }

        return $this->tableColumns[$table];
    }

    private function col(string $table, array $candidates): ?string
    {
        $cols = $this->columns($table);

        foreach ($candidates as $candidate) {
            $key = strtolower($candidate);
            if (isset($cols[$key])) {
                return $cols[$key];
            }
        }

        return null;
    }

    private function hasAnyColumn(string $table, array $candidates): bool
    {
        return $this->col($table, $candidates) !== null;
    }

    private function set(array &$data, string $table, array $candidates, mixed $value): void
    {
        $col = $this->col($table, $candidates);
        if ($col !== null) {
            $data[$col] = $value;
        }
    }

    private function applyTimestamps(string $table, array &$data, mixed $createdValue = null, mixed $updatedValue = null): void
    {
        $created = $createdValue ?? now()->toDateTimeString();
        $updated = $updatedValue ?? now()->toDateTimeString();

        $this->set($data, $table, ['created_at', 'createdat'], $created);
        $this->set($data, $table, ['updated_at', 'updatedat'], $updated);
    }

    private function rowKey(array $row, string $prefix): string
    {
        $id = $this->cleanString($row['id'] ?? null);

        if ($id !== null) {
            return $id;
        }

        return $prefix . '-' . uniqid();
    }

    private function cleanString(mixed $value, ?int $max = null): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $lower = strtolower($value);
        if (in_array($lower, ['null', 'n/a', 'na', 'none', 'unknown', '?'], true)) {
            return null;
        }

        if ($max !== null) {
            $value = mb_substr($value, 0, $max);
        }

        return $value;
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');

        return $value !== '' ? $value : 'unknown';
    }

    private function normalizePermalinkCandidate(?string $value, string $csvId, string $entityType): string
    {
        $clean = $this->cleanString($value);

        if ($clean === null) {
            $clean = '/' . $this->slug($entityType) . '/' . $csvId;
        }

        if (!str_starts_with($clean, '/')) {
            $clean = '/' . ltrim($clean, '/');
        }

        return mb_substr($clean, 0, 255);
    }

    private function uniquePermalink(mixed $value, string $csvId, string $entityType): string
    {
        $base = $this->normalizePermalinkCandidate((string) $value, $csvId, $entityType);
        $candidate = $base;
        $suffix = 1;

        $permalinkCol = $this->col('kompanite', ['permalink']);

        while (
            isset($this->permalinkIdMap[$candidate]) ||
            ($permalinkCol && DB::table('kompanite')->where($permalinkCol, $candidate)->exists())
        ) {
            $candidate = mb_substr(rtrim($base, '/') . '-' . $csvId . '-' . $suffix, 0, 255);
            $suffix++;
        }

        return $candidate;
    }

    // this method tries to return a company Name no matter what 
    // we try to generate a name from the fields : Name ,normalized_name,Permalink
    // if we dont manage  to get a name from any of those because they have no values 
    // we have a fall back that generates a random name 
    private function companyName(array $row, string $csvId, string $entityType): string
    {
        // get the name of the company from the row
        $name = $this->cleanString($row['name'] ?? null);
        // if the is a name, get the first 255 characters only 
        if ($name !== null) {
            return mb_substr($name, 0, 255);
        }
        // get the normalized name as it comes from the CSV column
        $normalized = $this->cleanString($row['normalized_name'] ?? null);
        // if there is a normalized name , get the first 255 characters of it 
        // after having replaced - and _ with spaces and capitalized the first letter of each word
        if ($normalized !== null) {
            return mb_substr(ucwords(str_replace(['-', '_'], ' ', $normalized)), 0, 255);
        }

        // get the permalink from the csv
        $permalink = $this->cleanString($row['permalink'] ?? null);

        // if permalink has value
        if ($permalink !== null) {
            // returns the last part of a path string
            $segment = trim((string) basename($permalink), '/');
            if ($segment !== '') {
                return mb_substr(ucwords(str_replace(['-', '_'], ' ', $segment)), 0, 255);
            }
        }

        return mb_substr("Unknown {$entityType} {$csvId}", 0, 255);
    }


    private function mapCompanyType(string $entityType, array $row): string
    {
        //get the value of the entity type from the CSV and normalize it (trim and lowercase)
        $entityType = strtolower(trim($entityType));

        // if the entityType in the CSV is Company or startup/
        //  we insert that as startup for the sake of this project
        if ($entityType === 'company' || $entityType === 'startup') {
            return 'startup';
        }
        // otherwise if the value of entityType is something else ,we insert that as "other"
        return 'other';
    }

    private function decimal(mixed $value, float $fallback = 0): float
    {
        if ($value === null) {
            return $fallback;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'n/a', 'na', '?'], true)) {
            return $fallback;
        }

        return (float) str_replace(',', '', $value);
    }

    private function intValue(mixed $value, int $fallback = 0): int
    {
        if ($value === null) {
            return $fallback;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'n/a', 'na', '?'], true)) {
            return $fallback;
        }

        return (int) $value;
    }

    private function boolValue(mixed $value, bool $fallback = false): bool
    {
        if ($value === null) {
            return $fallback;
        }

        $value = strtolower(trim((string) $value));

        if ($value === '' || in_array($value, ['null', 'n/a', 'na', '?'], true)) {
            return $fallback;
        }

        return in_array($value, ['1', 'true', 'yes', 'y'], true);
    }

    private function date(mixed $value, ?string $fallback = '1970-01-01'): ?string
    {
        if ($value === null) {
            return $fallback;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'n/a', 'na', '?'], true)) {
            return $fallback;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function datetime(mixed $value, string $fallback = '1970-01-01 00:00:00'): string
    {
        if ($value === null) {
            return $fallback;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', 'n/a', 'na', '?'], true)) {
            return $fallback;
        }

        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function mapStatus(mixed $value): ?string
    {
        $value = $this->cleanString($value);
        if ($value === null) {
            return null;
        }

        return match (strtolower($value)) {
            'operating', 'active' => 'operating',
            'closed' => 'closed',
            'acquired' => 'acquired',
            'ipo' => 'ipo',
            default => $value,
        };
    }

    private function placeholderUrl(string $segment, string $id): string
    {
        return "https://placeholder.local/{$segment}/{$id}";
    }

    private function nextIdForTable(string $table): int
    {
        return ((int) DB::table($table)->max('id')) + 1;
    }

    private function flushInsertBatch(string $table, array &$batch, bool $force = false): void
    {
        if (empty($batch)) {
            return;
        }

        if (!$force && count($batch) < self::BATCH_SIZE) {
            return;
        }

        DB::table($table)->insert($batch);
        $batch = [];
    }

    private function flushUpsertBatch(string $table, array &$batch, ?string $uniqueBy, bool $force = false): void
    {
        if (empty($batch)) {
            return;
        }

        if (!$force && count($batch) < self::BATCH_SIZE) {
            return;
        }

        if ($uniqueBy === null) {
            DB::table($table)->insert($batch);
            $batch = [];
            return;
        }

        $first = $batch[0];
        $updateColumns = array_values(array_filter(array_keys($first), function ($column) use ($uniqueBy) {
            return $column !== $uniqueBy && $column !== 'id';
        }));

        DB::table($table)->upsert($batch, [$uniqueBy], $updateColumns);
        $batch = [];
    }

    private function applyVerificationFields(string $table, array &$data): void
    {
        if ($this->hasAnyColumn($table, ['verification_status'])) {
            $this->set($data, $table, ['verification_status'], 'verified');
        }

        if ($this->hasAnyColumn($table, ['verified_at'])) {
            $this->set($data, $table, ['verified_at'], now()->toDateTimeString());
        }

        if ($this->hasAnyColumn($table, ['verified_by'])) {
            if (!isset($data[$this->col($table, ['verified_by'])])) {
                $this->set($data, $table, ['verified_by'], 0);
            }
        }
    }
}