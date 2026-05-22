<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only verified end users can claim or register companies
     */
    public function viewClaimCompany(User $user): bool
    {
        // Check if user has "end_user" role
        return $user->roli && $user->roli->role_name === 'end_user' && $user->verification_status === 'verified';
    }
}
