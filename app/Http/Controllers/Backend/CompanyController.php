<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Config;
use App\Prefecture;

class CompanyController extends Controller
{
    private function getRoute() {
        return 'company';
    }

    private static int $sequence = 1;

    protected function validator(array $data, $type) {
        // Determine if password validation is required depending on the calling
        return Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:100',
                // (update: not required, create: required)
                'postcode' => 'required|string|min:7|max:7',
                'prefecture' => 'required|string',
                'city' => 'required|string',
                'local' => 'required|string',
                'image' => 'required',
        ]);
    }

    public function index() {
        return view('backend.company.index');
    }

    public function add() 
    {
        $company = new Company();
        $company->form_action = $this->getRoute() . '.create';
        $company->page_title = 'Company Add Page';
        $company->page_type = 'create';
        return view('backend.company.form', [
            'company' => $company
        ]);
    }

    public function delete(Request $request) {
        try {
            // Get company by id
            $company = Company::find($request->get('id'));
            // If to-delete company is not the one currently logged in, proceed with delete attempt
            if (Auth::id() != $company->id) {

                // Delete company
                $company->delete();

                // If delete is successful
                return redirect()->route($this->getRoute())->with('success', Config::get('const.SUCCESS_DELETE_MESSAGE'));
            }
            // Send error if logged in user trying to delete himself
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_DELETE_SELF_MESSAGE'));
        } catch (Exception $e) {
            // If delete is failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_DELETE_MESSAGE'));
        }
    }

    public function edit($id) {
        $company = Company::find($id);
        $company->form_action = $this->getRoute() . '.update';
        $company->page_title = 'Company Edit Page';
        // Add page type here to indicate that the form.blade.php is in 'edit' mode
        $company->page_type = 'edit';
        return view('backend.company.form', [
            'company' => $company
        ]);
    }

    public function update(Request $request) {
        $newCompany = $request->all();
        $pref = \DB::table('prefectures')->find($newCompany['prefecture_id'])->display_name;
        $number = rand();
        $extension = $request->file("image")->getClientOriginalExtension();
        $image = $request->file("image");
        $path = Storage::disk("public_uploads")->putFileAs('files', $image, 'image_' . $number . '.' . $extension); 

        try {
            $currentCompany = Company::find($request->get('id'));

            if ($currentCompany) {
                $currentCompany->name = $newCompany['name'];
                $currentCompany->email = $newCompany['email'];
                $currentCompany->prefecture_id = $newCompany['prefecture_id'];
                $currentCompany->phone = $newCompany['phone'];
                $currentCompany->postcode = $newCompany['postcode'];
                $currentCompany->city = $newCompany['city'];
                $currentCompany->local = $newCompany['local'];
                $currentCompany->street_address = $pref . $newCompany['city'] . $newCompany['local'];
                $currentCompany->business_hour = $newCompany['business_hour'];
                $currentCompany->regular_holiday = $newCompany['regular_holiday'];
                $currentCompany->image = 'image_' . $number . '.' . $extension;
                $currentCompany->fax = $newCompany['fax'];
                $currentCompany->url = $newCompany['url'];
                $currentCompany->license_number = $newCompany['license_number'];
                $currentCompany->save();
                // If update is successful
                return redirect()->route($this->getRoute())->with('success', Config::get('const.SUCCESS_UPDATE_MESSAGE'));
            } else {
                // If update is failed
                return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_UPDATE_MESSAGE'));
            }
        } catch (Exception $e) {
            // If update is failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_UPDATE_MESSAGE'));
        }
    }

    public function create(Request $request) {
        $newCompany = $request->all();

        $number = rand();

        $pref = \DB::table('prefectures')->find($newCompany['prefecture_id'])->display_name;
        
        //拡張子付きでファイル名を取得
        $filenameWithExt = $request->file("image")->getClientOriginalName();
        
        //ファイル名のみを取得
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        
        //拡張子を取得
        $extension = $request->file("image")->getClientOriginalExtension();
        
        $image = $request->file("image");
        $path = Storage::disk("public_uploads")->putFileAs('files', $image, 'image_' . $number . '.' . $extension); 

        try {
            $newCompany['street_address'] = $pref . $newCompany['city'] . $newCompany['local'];
            $newCompany['image'] = 'image_' . $number . '.' . $extension;
            $company = Company::create($newCompany);
            if ($company) {
                // Create is successful, back to list
                return redirect()->route($this->getRoute())->with('success', Config::get('const.SUCCESS_CREATE_MESSAGE'));
            } else {
                // Create is failed
                return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_CREATE_MESSAGE'));
            }
        } catch (Exception $e) {
            // Create is failed
            return redirect()->route($this->getRoute())->with('error', Config::get('const.FAILED_CREATE_MESSAGE'));
        }
    }
}
