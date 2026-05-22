<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Startup Management System</title>

    <link href="https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@500,700,800&f[]=satoshi@400,500,700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Satoshi', sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: min(1120px, 92%);
            margin: 0 auto;
        }

        .topbar {
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 20px 0;
            background: #0f172a;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-family: 'Cabinet Grotesk', sans-serif;
        }

        .brand-name {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .hero {
            padding: 90px 0 70px;
            text-align: center;
            background: linear-gradient(180deg, #0f172a 0%, #172554 100%);
        }

        .hero h1 {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: clamp(2.5rem, 7vw, 4.8rem);
            line-height: 1.05;
            letter-spacing: -0.04em;
            max-width: 10ch;
            margin: 0 auto 20px;
        }

        .hero p {
            max-width: 700px;
            margin: 0 auto 36px;
            font-size: 1.05rem;
            color: #cbd5e1;
        }

        .hero .cta {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 18px 34px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            transition: 0.2s ease;
        }

        .hero .cta:hover {
            background: #1d4ed8;
        }

        .intro {
            padding: 70px 0 30px;
        }

        .intro h2 {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: clamp(1.8rem, 4vw, 3rem);
            letter-spacing: -0.03em;
            margin-bottom: 14px;
        }

        .intro p {
            max-width: 760px;
            color: #94a3b8;
        }

        .features {
            padding: 20px 0 90px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 18px;
        }

        .card {
            background: #111827;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 26px;
        }

        .card h3 {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: 1.35rem;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .card p {
            color: #cbd5e1;
            font-size: 0.98rem;
        }

        .card ul {
            margin-top: 14px;
            padding-left: 18px;
            color: #cbd5e1;
        }

        .card li + li {
            margin-top: 8px;
        }

        .card-wide {
            grid-column: span 7;
        }

        .card-tall {
            grid-column: span 5;
        }

        .card-half {
            grid-column: span 6;
        }

        .bottom-cta {
            padding: 30px 0 90px;
            text-align: center;
        }

        .bottom-cta h2 {
            font-family: 'Cabinet Grotesk', sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            letter-spacing: -0.03em;
            margin-bottom: 12px;
        }

        .bottom-cta p {
            color: #94a3b8;
            margin-bottom: 28px;
        }

        .footer {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding: 24px 0;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        @media (max-width: 900px) {
            .card-wide,
            .card-tall,
            .card-half {
                grid-column: span 12;
            }

            .hero {
                padding: 70px 0 55px;
            }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="container">
            <div class="brand">
                <div class="brand-mark">S</div>
                <div class="brand-name">Startup Management System</div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Manage startup data with clarity.</h1>
                <p>
                    A centralized platform for registering startups, tracking funding activity,
                    managing investor and fund records, reviewing milestones, and producing
                    searchable analytical information from one structured system.
                </p>
                <a href="{{ route('login') }}" class="cta">Access Data Now</a>
            </div>
        </section>

        <section class="intro">
            <div class="container">
                <h2>What this system helps you do</h2>
                <p>
                    The platform is built around the real workflow of startup data management:
                    record entities, connect related information, support reporting, and verify
                    what has been submitted before it becomes trusted data.
                </p>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="feature-grid">

                    <article class="card card-wide">
                        <h3>Register and manage core startup records</h3>
                        <p>
                            Create and maintain structured records for startups, companies,
                            investors, and funds in one place. The system is meant to support
                            registration, updates, and organized storage of linked entities rather
                            than isolated data entry.
                        </p>
                        <ul>
                            <li>Startup and company profiles</li>
                            <li>Fund and investor records</li>
                            <li>Updates to existing entities</li>
                            <li>Centralized management of related data</li>
                        </ul>
                    </article>

                    <article class="card card-tall">
                        <h3>Track funding rounds and investments</h3>
                        <p>
                            Record funding rounds with amounts, dates, round types, and valuation
                            details, then connect each round with the individual investments that
                            took place inside it.
                        </p>
                        <ul>
                            <li>Seed, Series A, Series B, and later rounds</li>
                            <li>Individual investor participation</li>
                            <li>Raised amounts and currencies</li>
                            <li>Pre-money valuation data</li>
                        </ul>
                    </article>

                    <article class="card card-half">
                        <h3>Search, filter, and compare records</h3>
                        <p>
                            Users can explore the registry through search and filtering based on
                            sector, period, investor, funding type, or startup stage, making the
                            system useful both for management and for analysis.
                        </p>
                    </article>

                    <article class="card card-half">
                        <h3>Generate KPI-focused insights</h3>
                        <p>
                            The platform is designed to support reporting and KPI views such as
                            total funding, number of rounds, average funding per round, annual
                            funding trends, and time to first funding.
                        </p>
                    </article>

                    <article class="card card-half">
                        <h3>Capture business events and growth history</h3>
                        <p>
                            Beyond funding, the system also tracks important company events such as
                            milestones, acquisitions, and IPO-related information, so the dataset
                            reflects the wider evolution of each startup or company.
                        </p>
                    </article>

                    <article class="card card-half">
                        <h3>Keep people, roles, and relationships connected</h3>
                        <p>
                            Founders, executives, board members, and other individuals can be tied
                            to companies through relationship records that preserve titles, time
                            periods, and past or current affiliations.
                        </p>
                    </article>

                    <article class="card card-wide">
                        <h3>Support verification and trusted data review</h3>
                        <p>
                            The project includes a verification workflow where submitted records can
                            be reviewed, approved, rejected, or flagged for review. This separates
                            declared data from confirmed data and makes the system more reliable for
                            reporting and decision-making.
                        </p>
                        <ul>
                            <li>Pending record review</li>
                            <li>Status-based verification flow</li>
                            <li>More reliable analytical outputs</li>
                            <li>Better auditability of submitted information</li>
                        </ul>
                    </article>

                    <article class="card card-tall">
                        <h3>Store offices and supporting details</h3>
                        <p>
                            Company offices and location details can also be recorded, including
                            region, city, postal code, and geographic coordinates, which makes the
                            system broader than only funding data.
                        </p>
                    </article>

                </div>
            </div>
        </section>

        <section class="bottom-cta">
            <div class="container">
                <h2>Enter the platform</h2>
                <p>Log in to manage records, review data, or explore the startup ecosystem.</p>
                <a href="{{ route('login') }}" class="cta">Access Data Now</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            Startup Management System · Albania
        </div>
    </footer>

</body>
</html>