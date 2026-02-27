<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicinePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Medicine $medicine): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

//    public function delete(User $user, Medicine $medicine): bool
//    {
//        return $this->update($user, $medicine);
//    }
//
//    public function update(User $user, Medicine $medicine): bool
//    {
//        $tenant = Filament::getTenant();
//
//        if ($tenant === null) {
//            return $medicine->ClinicId === null;
//        }
//
//        return $medicine->ClinicId === $tenant->Id;
//    }


}
