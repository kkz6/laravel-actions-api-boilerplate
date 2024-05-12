<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * The permissions every model should be given
     */
    protected array $modelPermissions = [
        'view any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force delete',
    ];

    /**
     * The permissions to be given to the Admin role
     */
    protected array $adminPermissions = [
        User::class      => true,
    ];

    //Permissions aren't required for view and view any as everyone can do this
    protected array $userPermissions = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->handlePermissions();
        $this->handleRoles();
    }

    /**
     * Generates the permissions for the site
     */
    protected function handlePermissions(): void
    {
        //By default, every model is given every permission even if some won't be used throughout the site
        Collection::make([
            User::class,
        ])
            ->each(
                callback: fn (string $modelName) => Collection::make(items: $this->modelPermissions)
                    ->each(
                        callback: fn ($permission) => Permission::findOrCreate(
                            $this->getPermissionName(permission: $permission, model: $modelName)
                        )
                    )
            );
    }

    /**
     * Generates the roles for the site and assigns their permissions
     */
    protected function handleRoles(): void
    {
        Role::findOrCreate('Admin')
            ->givePermissionTo(
                $this->generatePermissions(
                    permissions: $this->adminPermissions
                )
            );

        Role::findOrCreate('User')
            ->givePermissionTo(
                $this->generatePermissions(
                    permissions: $this->userPermissions
                )
            );
    }

    /**
     * Creates an array of permissions to assign to a role
     *
     * @param array<class-string> $permissions
     *
     * @return array<string>
     */
    protected function generatePermissions(array $permissions): array
    {
        return Arr::flatten(
            Arr::map(
                array: $permissions,
                callback: fn (array|bool $permission, string $modelName) => Arr::map(
                    array: is_bool($permission) ? $this->modelPermissions : $permission,
                    callback: fn (string $permission) => $this->getPermissionName($permission, $modelName)
                )
            )
        );
    }

    /**
     * Gets the name of the model for the permission from the class name
     *
     * @param class-string $model
     */
    protected function getModelPermissionName(string $model): string
    {
        return Str::singular(value: (new $model)->getTable());
    }

    /**
     * Gets the name of the permission from the permission and class string
     *
     * @param class-string $model
     */
    protected function getPermissionName(string $permission, string $model): string
    {
        return "$permission {$this->getModelPermissionName(model:$model)}";
    }
}
