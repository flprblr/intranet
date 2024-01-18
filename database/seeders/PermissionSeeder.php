<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIndex = new Permission;
        $userIndex->name = 'user.index';
        $userIndex->guard_name = 'web';
        $userIndex->save();

        $userShow = new Permission;
        $userShow->name = 'user.show';
        $userShow->guard_name = 'web';
        $userShow->save();

        $userCreate = new Permission;
        $userCreate->name = 'user.create';
        $userCreate->guard_name = 'web';
        $userCreate->save();

        $userEdit = new Permission;
        $userEdit->name = 'user.edit';
        $userEdit->guard_name = 'web';
        $userEdit->save();

        $userDestroy = new Permission;
        $userDestroy->name = 'user.destroy';
        $userDestroy->guard_name = 'web';
        $userDestroy->save();

        /* Order*/
        $userIndex = new Permission;
        $userIndex->name = 'order.index';
        $userIndex->guard_name = 'web';
        $userIndex->save();

        $userShow = new Permission;
        $userShow->name = 'order.show';
        $userShow->guard_name = 'web';
        $userShow->save();

        $userCreate = new Permission;
        $userCreate->name = 'order.create';
        $userCreate->guard_name = 'web';
        $userCreate->save();

        $userEdit = new Permission;
        $userEdit->name = 'order.edit';
        $userEdit->guard_name = 'web';
        $userEdit->save();

        $userDestroy = new Permission;
        $userDestroy->name = 'order.destroy';
        $userDestroy->guard_name = 'web';
        $userDestroy->save(); /**/


        /* Extguarantee*/
        $userIndex = new Permission;
        $userIndex->name = 'extguarantee.index';
        $userIndex->guard_name = 'web';
        $userIndex->save();

        $userShow = new Permission;
        $userShow->name = 'extguarantee.show';
        $userShow->guard_name = 'web';
        $userShow->save();

        $userCreate = new Permission;
        $userCreate->name = 'extguarantee.create';
        $userCreate->guard_name = 'web';
        $userCreate->save();

        $userEdit = new Permission;
        $userEdit->name = 'extguarantee.edit';
        $userEdit->guard_name = 'web';
        $userEdit->save();

        $userDestroy = new Permission;
        $userDestroy->name = 'extguarantee.destroy';
        $userDestroy->guard_name = 'web';
        $userDestroy->save(); /**/



        $roleIndex = new Permission;
        $roleIndex->name = 'role.index';
        $roleIndex->guard_name = 'web';
        $roleIndex->save();

        $roleShow = new Permission;
        $roleShow->name = 'role.show';
        $roleShow->guard_name = 'web';
        $roleShow->save();

        $roleCreate = new Permission;
        $roleCreate->name = 'role.create';
        $roleCreate->guard_name = 'web';
        $roleCreate->save();

        $roleEdit = new Permission;
        $roleEdit->name = 'role.edit';
        $roleEdit->guard_name = 'web';
        $roleEdit->save();

        $roleDestroy = new Permission;
        $roleDestroy->name = 'role.destroy';
        $roleDestroy->guard_name = 'web';
        $roleDestroy->save();



        $permissionIndex = new Permission;
        $permissionIndex->name = 'permission.index';
        $permissionIndex->guard_name = 'web';
        $permissionIndex->save();

        $permissionShow = new Permission;
        $permissionShow->name = 'permission.show';
        $permissionShow->guard_name = 'web';
        $permissionShow->save();

        $permissionCreate = new Permission;
        $permissionCreate->name = 'permission.create';
        $permissionCreate->guard_name = 'web';
        $permissionCreate->save();

        $permissionEdit = new Permission;
        $permissionEdit->name = 'permission.edit';
        $permissionEdit->guard_name = 'web';
        $permissionEdit->save();

        $permissionDestroy = new Permission;
        $permissionDestroy->name = 'permission.destroy';
        $permissionDestroy->guard_name = 'web';
        $permissionDestroy->save();



        $stateIndex = new Permission;
        $stateIndex->name = 'state.index';
        $stateIndex->guard_name = 'web';
        $stateIndex->save();

        $stateShow = new Permission;
        $stateShow->name = 'state.show';
        $stateShow->guard_name = 'web';
        $stateShow->save();

        $stateCreate = new Permission;
        $stateCreate->name = 'state.create';
        $stateCreate->guard_name = 'web';
        $stateCreate->save();

        $stateEdit = new Permission;
        $stateEdit->name = 'state.edit';
        $stateEdit->guard_name = 'web';
        $stateEdit->save();

        $stateDestroy = new Permission;
        $stateDestroy->name = 'state.destroy';
        $stateDestroy->guard_name = 'web';
        $stateDestroy->save();



        $cityIndex = new Permission;
        $cityIndex->name = 'city.index';
        $cityIndex->guard_name = 'web';
        $cityIndex->save();

        $cityShow = new Permission;
        $cityShow->name = 'city.show';
        $cityShow->guard_name = 'web';
        $cityShow->save();

        $cityCreate = new Permission;
        $cityCreate->name = 'city.create';
        $cityCreate->guard_name = 'web';
        $cityCreate->save();

        $cityEdit = new Permission;
        $cityEdit->name = 'city.edit';
        $cityEdit->guard_name = 'web';
        $cityEdit->save();

        $cityDestroy = new Permission;
        $cityDestroy->name = 'city.destroy';
        $cityDestroy->guard_name = 'web';
        $cityDestroy->save();



        $storeIndex = new Permission;
        $storeIndex->name = 'store.index';
        $storeIndex->guard_name = 'web';
        $storeIndex->save();

        $storeShow = new Permission;
        $storeShow->name = 'store.show';
        $storeShow->guard_name = 'web';
        $storeShow->save();

        $storeCreate = new Permission;
        $storeCreate->name = 'store.create';
        $storeCreate->guard_name = 'web';
        $storeCreate->save();

        $storeEdit = new Permission;
        $storeEdit->name = 'store.edit';
        $storeEdit->guard_name = 'web';
        $storeEdit->save();

        $storenDestroy = new Permission;
        $storenDestroy->name = 'store.destroy';
        $storenDestroy->guard_name = 'web';
        $storenDestroy->save();



        $stationIndex = new Permission;
        $stationIndex->name = 'station.index';
        $stationIndex->guard_name = 'web';
        $stationIndex->save();

        $stationShow = new Permission;
        $stationShow->name = 'station.show';
        $stationShow->guard_name = 'web';
        $stationShow->save();

        $stationCreate = new Permission;
        $stationCreate->name = 'station.create';
        $stationCreate->guard_name = 'web';
        $stationCreate->save();

        $stationEdit = new Permission;
        $stationEdit->name = 'station.edit';
        $stationEdit->guard_name = 'web';
        $stationEdit->save();

        $stationDestroy = new Permission;
        $stationDestroy->name = 'station.destroy';
        $stationDestroy->guard_name = 'web';
        $stationDestroy->save();



        $serviceIndex = new Permission;
        $serviceIndex->name = 'service.index';
        $serviceIndex->guard_name = 'web';
        $serviceIndex->save();

        $serviceShow = new Permission;
        $serviceShow->name = 'service.show';
        $serviceShow->guard_name = 'web';
        $serviceShow->save();

        $serviceCreate = new Permission;
        $serviceCreate->name = 'service.create';
        $serviceCreate->guard_name = 'web';
        $serviceCreate->save();

        $serviceEdit = new Permission;
        $serviceEdit->name = 'service.edit';
        $serviceEdit->guard_name = 'web';
        $serviceEdit->save();

        $serviceDestroy = new Permission;
        $serviceDestroy->name = 'service.destroy';
        $serviceDestroy->guard_name = 'web';
        $serviceDestroy->save();



        $assurantIndex = new Permission;
        $assurantIndex->name = 'assurant.index';
        $assurantIndex->guard_name = 'web';
        $assurantIndex->save();



        $salesIndex = new Permission;
        $salesIndex->name = 'sales.index';
        $salesIndex->guard_name = 'web';
        $salesIndex->save();



        $companyIndex = new Permission;
        $companyIndex->name = 'company.index';
        $companyIndex->guard_name = 'web';
        $companyIndex->save();

        $companyShow = new Permission;
        $companyShow->name = 'company.show';
        $companyShow->guard_name = 'web';
        $companyShow->save();

        $companyCreate = new Permission;
        $companyCreate->name = 'company.create';
        $companyCreate->guard_name = 'web';
        $companyCreate->save();

        $companyEdit = new Permission;
        $companyEdit->name = 'company.edit';
        $companyEdit->guard_name = 'web';
        $companyEdit->save();

        $companyDestroy = new Permission;
        $companyDestroy->name = 'company.destroy';
        $companyDestroy->guard_name = 'web';
        $companyDestroy->save();



        $ecommerceIndex = new Permission;
        $ecommerceIndex->name = 'ecommerce.index';
        $ecommerceIndex->guard_name = 'web';
        $ecommerceIndex->save();

        $ecommerceShow = new Permission;
        $ecommerceShow->name = 'ecommerce.show';
        $ecommerceShow->guard_name = 'web';
        $ecommerceShow->save();

        $ecommerceCreate = new Permission;
        $ecommerceCreate->name = 'ecommerce.create';
        $ecommerceCreate->guard_name = 'web';
        $ecommerceCreate->save();

        $ecommerceEdit = new Permission;
        $ecommerceEdit->name = 'ecommerce.edit';
        $ecommerceEdit->guard_name = 'web';
        $ecommerceEdit->save();

        $ecommerceDestroy = new Permission;
        $ecommerceDestroy->name = 'ecommerce.destroy';
        $ecommerceDestroy->guard_name = 'web';
        $ecommerceDestroy->save();

        $ecommerceDestroy = new Permission;
        $ecommerceDestroy->name = 'customer.index';
        $ecommerceDestroy->guard_name = 'web';
        $ecommerceDestroy->save();

        /* Order*/
        $mvorderIndex = new Permission;
        $mvorderIndex->name = 'mvorder.index';
        $mvorderIndex->guard_name = 'web';
        $mvorderIndex->save();

        $mvorderShow = new Permission;
        $mvorderShow->name = 'mvorder.show';
        $mvorderShow->guard_name = 'web';
        $mvorderShow->save();

        $mvorderCreate = new Permission;
        $mvorderCreate->name = 'mvorder.create';
        $mvorderCreate->guard_name = 'web';
        $mvorderCreate->save();

        $mvorderEdit = new Permission;
        $mvorderEdit->name = 'mvorder.edit';
        $mvorderEdit->guard_name = 'web';
        $mvorderEdit->save();

        $mvorderDestroy = new Permission;
        $mvorderDestroy->name = 'mvorder.destroy';
        $mvorderDestroy->guard_name = 'web';
        $mvorderDestroy->save(); /**/

    }
}
