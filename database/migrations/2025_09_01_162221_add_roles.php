<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $roleAdmin = new Role();
        $roleAdmin->id = Role::ROLE_ADMIN['id'];
        $roleAdmin->name = Role::ROLE_ADMIN['name'];
        $roleAdmin->slug = Role::ROLE_ADMIN['slug'];
        $roleAdmin->save();

        $roleEditor = new Role();
        $roleEditor->id = Role::ROLE_EDITOR['id'];
        $roleEditor->name = Role::ROLE_EDITOR['name'];
        $roleEditor->slug = Role::ROLE_EDITOR['slug'];
        $roleEditor->save();

        $roleGuest = new Role();
        $roleGuest->id = Role::ROLE_GUEST['id'];
        $roleGuest->name = Role::ROLE_GUEST['name'];
        $roleGuest->slug = Role::ROLE_GUEST['slug'];
        $roleGuest->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
