<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request): View
    {
        $roles = Role::orderBy('name')->paginate(5);

        return view('roles.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $permission = Permission::get();

        return view('roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->input('name')]);

        if ($role) {
            $role->syncPermissions(
                array_map(fn ($value) => (int) $value, $request->input('permission', []))
            );

            return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');
        }

        return redirect()->back()->with('error', 'Échec de la création du rôle. Veuillez réessayer.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id): RedirectResponse
    {
        // Validation déjà effectuée par UpdateRoleRequest
        $role = Role::find($id);

        if (! $role) {
            return redirect()->route('roles.index')->with('error', 'Rôle non trouvé.');
        }

        $role->name = $request->input('name');
        $role->save();

        // Synchronisation des permissions
        $permissionsID = array_map(function ($value) {
            return (int) $value;
        }, $request->input('permission'));

        $role->syncPermissions($permissionsID);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        DB::table('roles')->where('id', $id)->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès');
    }
}
