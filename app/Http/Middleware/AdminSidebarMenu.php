<?php

namespace App\Http\Middleware;

use App\Utils\ModuleUtil;
use Closure;
use Menu;

class AdminSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        Menu::create('admin-sidebar-menu', function ($menu) {
            
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

            $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
            $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];

            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
            //Home
            $menu->url(action('HomeController@index'), __('home.home'), ['icon' => 'fa fas fa-tachometer-alt', 'active' => request()->segment(1) == 'home'])->order(5);

            //User management dropdown
            if (auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view')) {
                $menu->dropdown(
                    __('user.user_management'),
                    function ($sub) {
                        if (auth()->user()->can('user.view')) {
                            $sub->url(
                                action('ManageUserController@index'),
                                __('user.users'),
                                ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'users']
                            );
                        }
                        if (auth()->user()->can('roles.view')) {
                            $sub->url(
                                action('RoleController@index'),
                                __('user.roles'),
                                ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(1) == 'roles']
                            );
                        }
                        if (auth()->user()->can('user.create')) {
                            $sub->url(
                                action('SalesCommissionAgentController@index'),
                                __('lang_v1.sales_commission_agents'),
                                ['icon' => 'fa fas fa-handshake', 'active' => request()->segment(1) == 'sales-commission-agents']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-users']
                )->order(10);
            }

           
                 //Package dropdown
                 if (auth()->user()->can('product.view') || auth()->user()->can('product.create') ||
                 auth()->user()->can('brand.view') || auth()->user()->can('unit.view') ||
                 auth()->user()->can('category.view') || auth()->user()->can('brand.create') ||
                 auth()->user()->can('unit.create') || auth()->user()->can('category.create')) {
                 $menu->dropdown(
                     __('lang_v1.package'),
                     function ($sub) {
                         if (auth()->user()->can('product.view')) {
                             $sub->url(
                                 action('PackageController@index'),
                                 __('lang_v1.list_package'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                             );
                         }
                            if (auth()->user()->can('product.view')) {
                             $sub->url(
                                 action('ThePackageController@index'),
                                 __('lang_v1.list_package_out'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                             );
                         }
                         if (auth()->user()->can('product.create')) {
                             $sub->url(
                                 action('ThePackageController@create'),
                                 __('lang_v1.add_package_out'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                             );
                         }
                                 if (auth()->user()->can('product.view')) {
                             $sub->url(
                                 action('packingListController@index'),
                                 __('lang_v1.packing_list'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                             );
                         }
                         if (auth()->user()->can('product.create')) {
                             $sub->url(
                                 action('packingListController@create'),
                                 __('lang_v1.add_packing_list'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                             );
                         }
                 
                 
                      
                     },
                     ['icon' => 'fa fas fa-cubes', 'id' => 'tour_step5']
                 )->order(20);
             }

         

        });
        
        //Add menus from modules
        $moduleUtil = new ModuleUtil;
        $moduleUtil->getModuleData('modifyAdminMenu');

        return $next($request);
    }
}