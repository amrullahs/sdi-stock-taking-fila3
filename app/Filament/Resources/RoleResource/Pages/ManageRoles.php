<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManageRoles extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.resources.role-resource.pages.manage-roles';

    protected static ?string $title = 'Manage Role Permissions';

    protected static ?string $navigationLabel = 'Manage Permissions';

    public ?array $data = [];

    public ?int $selectedRoleId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Select Role')
                    ->schema([
                        Select::make('selectedRoleId')
                            ->label('Role')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->placeholder('Select a role to manage permissions')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedRoleId = $state;
                                $this->loadRolePermissions();
                            }),
                    ])
                    ->columns(1),

                ...$this->getPermissionSections(),
            ])
            ->statePath('data');
    }

    protected function getPermissionSections(): array
    {
        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissionsByResource($permissions);
        $sections = [];

        foreach ($groupedPermissions as $resource => $resourcePermissions) {
            $sections[] = Section::make(ucfirst(str_replace('_', ' ', $resource)))
                ->schema([
                    CheckboxList::make("permissions.{$resource}")
                        ->label('')
                        ->options($resourcePermissions->pluck('name', 'name'))
                        ->descriptions($resourcePermissions->mapWithKeys(function ($permission) {
                            return [$permission->name => $this->getPermissionDescription($permission->name)];
                        })->toArray())
                        ->columns(1)
                        ->gridDirection('row')
                ])
                ->visible(fn() => $this->selectedRoleId !== null)
                ->collapsible()
                ->persistCollapsed()
                ->compact();
        }

        return $sections;
    }

    protected function groupPermissionsByResource(Collection $permissions): Collection
    {
        return $permissions->groupBy(function ($permission) {
            // Extract resource name from permission name
            // e.g., 'view_any_user' -> 'user', 'create_line_sto' -> 'line_sto'
            $parts = explode('_', $permission->name);

            // Remove action words from the beginning
            $actionWords = ['view', 'any', 'create', 'update', 'delete', 'restore', 'force', 'replicate', 'reorder'];

            $resourceParts = [];
            $skipNext = false;

            foreach ($parts as $index => $part) {
                if ($skipNext) {
                    $skipNext = false;
                    continue;
                }

                if (in_array($part, $actionWords)) {
                    if ($part === 'view' && isset($parts[$index + 1]) && $parts[$index + 1] === 'any') {
                        $skipNext = true;
                    }
                    continue;
                }

                $resourceParts[] = $part;
            }

            return implode('_', $resourceParts) ?: 'general';
        });
    }

    protected function getPermissionDescription(string $permissionName): string
    {
        $descriptions = [
            'view_any' => 'View any records',
            'view' => 'View individual records',
            'create' => 'Create new records',
            'update' => 'Update existing records',
            'delete' => 'Delete records',
            'restore' => 'Restore deleted records',
            'force_delete' => 'Permanently delete records',
            'replicate' => 'Duplicate records',
            'reorder' => 'Reorder records',
        ];

        foreach ($descriptions as $action => $description) {
            if (str_starts_with($permissionName, $action)) {
                return $description;
            }
        }

        return 'Custom permission';
    }

    public function loadRolePermissions(): void
    {
        if (!$this->selectedRoleId) {
            return;
        }

        $role = Role::find($this->selectedRoleId);
        if (!$role) {
            return;
        }

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissionsByResource($permissions);

        $permissionsData = [];
        foreach ($groupedPermissions as $resource => $resourcePermissions) {
            $permissionsData[$resource] = array_intersect(
                $resourcePermissions->pluck('name')->toArray(),
                $rolePermissions
            );
        }

        // Update the data property directly
        $this->data = [
            'selectedRoleId' => $this->selectedRoleId,
            'permissions' => $permissionsData
        ];

        // Debug: Log the data being filled
        Log::info('Loading role permissions', [
            'role_id' => $this->selectedRoleId,
            'role_name' => $role->name,
            'role_permissions_count' => count($rolePermissions),
            'permissions_data' => $permissionsData,
            'data_property' => $this->data
        ]);

        // Fill the form with the data
        $this->form->fill($this->data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Permissions')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn() => $this->selectedRoleId !== null)
                ->action('savePermissions'),
        ];
    }

    public function savePermissions(): void
    {
        if (!$this->selectedRoleId) {
            return;
        }

        $role = Role::find($this->selectedRoleId);
        if (!$role) {
            return;
        }

        // Get form state and update data property
        $formState = $this->form->getState();
        $this->data = $formState;

        $selectedPermissions = [];

        // Debug: Log form state
        Log::info('Form state when saving', [
            'form_state' => $formState,
            'data_property' => $this->data,
            'selected_role_id' => $this->selectedRoleId
        ]);

        if (isset($this->data['permissions'])) {
            foreach ($this->data['permissions'] as $resourcePermissions) {
                if (is_array($resourcePermissions)) {
                    $selectedPermissions = array_merge($selectedPermissions, $resourcePermissions);
                }
            }
        }

        $role->syncPermissions($selectedPermissions);

        Notification::make()
            ->title('Permissions Updated')
            ->body("Permissions for role '{$role->name}' have been updated successfully.")
            ->success()
            ->send();
    }

    public static function canAccess(array $parameters = []): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->can('update_role') ?? false;
    }
}
