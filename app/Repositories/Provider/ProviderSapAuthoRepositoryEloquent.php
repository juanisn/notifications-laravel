<?php

namespace App\Repositories\Provider;

use App\Models\Provider\ProviderSap;
use App\Models\Provider\ProviderSapAuthorization;
use App\Models\Provider\ProviderSapAuthorizationLog;
use App\Models\User;
use App\Repositories\AppRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Spatie\Permission\Models\Role;

/**
 * Class ProviderSapRepositoryEloquent.
 *
 * @package namespace App\Repositories\Provider;
 */
class ProviderSapAuthoRepositoryEloquent extends AppRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */

     /* 
     * Compras, Legal, Tesoreria, Auditoria
     */
    private $roleIdAuthorizators = [3,4,5,6,7];

    public function model()
    {
        return ProviderSapAuthorization::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getProviderSapWithAuthorizationMap($id = 0){

        if($id == 0)
            return null;

        $providerSap = ProviderSap::find($id)->load('authorizations.user.roles');
        $providerSap->authorizes = $this->findAuthorizateList($providerSap);
        
        return $providerSap;
    }

    public function findAuthorizateList($providerSap): array {
        
        $roleAuthorizators = Role::whereIn('id', $this->roleIdAuthorizators)->get();
        $authorizationsList = array();
        
        foreach ($roleAuthorizators as $role) {
            
            $authorize = [];

            $temp = $providerSap->authorizations->filter(function($item) use ($role){
                return $item->user->roles->find($role->id)? true : false;
            });

            foreach ($temp as $value) {
                $authorize[] = $value;
            }

            array_push($authorizationsList, compact('role', 'authorize'));
        }


        return $authorizationsList;
    }
    
}
    