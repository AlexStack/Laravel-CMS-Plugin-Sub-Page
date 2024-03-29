<?php

namespace Amila\LaravelCms\Plugins\SubPage\Controllers;

use AlexStack\LaravelCms\Helpers\LaravelCmsHelper;
use AlexStack\LaravelCms\Models\LaravelCmsPage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubPageController extends Controller
{
    private $user = null;
    private $helper;

    public function __construct()
    {
        $this->helper = new LaravelCmsHelper();
    }

    public function checkUser()
    {
        if (! $this->user) {
            $this->user = $this->helper->hasPermission();
        }
    }

    // public function create()
    // {
    // }

    public function edit($id, $page)
    {
        // uncomment exit() line to make sure your plugin method invoked
        // please check the php_class value if not invoked

        //exit('Looks good, the plugin\'s edit() method invoked. id='.$id.' <hr> FILE='.__FILE__.' <hr> PAGE TITLE='.$page->title);

        if ('yes' == request()->sort_top_level_menu) {
            $data['top_level_menus'] = LaravelCmsPage::whereNull('parent_id')->where('menu_enabled', 1)->orderBy('sort_value', 'desc')->orderBy('id', 'desc')->get();

            return $data;
        } else {
            $parent_pages = LaravelCmsPage::with('parent:title,menu_title,id,parent_id,menu_enabled,slug,redirect_url,sort_value,status')
                ->where('id', $id)
                ->first(['title', 'menu_title', 'id', 'parent_id', 'menu_enabled', 'slug', 'redirect_url', 'sort_value', 'status']);

            $data['flat_parent_pages'] = array_reverse($this->flattenParentArray($parent_pages->toArray()));

            return $data;
        }

        return $id;
    }

    // public function store($form_data, $page, $plugin_settings)
    // {
    //     return $this->update($form_data, $page, $plugin_settings);
    // }

    public function update($form_data, $page, $plugin_settings)
    {
        //$this->helper->debug($form_data);
        if (strpos($form_data['pages_new_order'], ',') && strpos($form_data['pages_new_sort'], ',')) {
            $page_ids = explode(',', $form_data['pages_new_order']);
            $old_sort = explode(',', $form_data['pages_old_sort']);
            $new_sort = explode(',', $form_data['pages_new_sort']);
            if (count($new_sort) != count($page_ids)) {
                return false;
            }
            foreach ($page_ids as $key => $page_id) {
                if ($old_sort[$key] == $new_sort[$key]) {
                    continue; // ignore
                }
                LaravelCmsPage::where('id', $page_id)->update(['sort_value'=>$new_sort[$key]]);
            }

            return true;
        }
    }

    // public function destroy(Request $request, $id)
    // {
    //     //
    // }

    /*
     * Other methods.
     */

    public function flattenParentArray($element, $name = 'parent', $depth = 0)
    {
        $result = [];

        $element['depth'] = $depth;

        if (isset($element[$name])) {
            $children = $element[$name];
            unset($element[$name]);
        } else {
            $children = null;
        }

        $result[] = $element;

        if (isset($children)) {
            $result = array_merge($result, $this->flattenParentArray($children, $name, $depth + 1));
        }

        return $result;
    }
}
