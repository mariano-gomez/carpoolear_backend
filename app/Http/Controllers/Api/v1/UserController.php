<?php

namespace STS\Http\Controllers\Api\v1;

use STS\Entities\Donation;
use Illuminate\Http\Request;
use STS\Http\Controllers\Controller;
use STS\Transformers\ProfileTransformer;
use Dingo\Api\Exception\ResourceException;
use STS\Contracts\Logic\User as UserLogic;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->middleware('logged', ['except' => ['create', 'registerDonation', 'bankData', 'terms']]);
        $this->userLogic = $userLogic;
    }

    public function create(Request $request)
    {
        $data = $request->all();
        if (config('carpoolear.module_validated_drivers', false))  {
            $files = $request->file('driver_data_docs');
            if (!empty($files)) {
                $docs = array();
                foreach($files as $file) {
                    $tempDoc = $this->userLogic->uploadDoc($file);
                    if (!$tempDoc) {
                        // return response()->json('La imagen' . $file->getClientOriginalName() . $file->getClientOriginalExtension() . ' supera los 4MB.', 422);
                    }
                    $docs[] = $tempDoc;
                }
                $data['driver_data_docs'] = json_encode($docs);
            }
        }
        $user = $this->userLogic->create($data);
        if (! $user) {
            throw new StoreResourceFailedException('Could not create new user.', $this->userLogic->getErrors());
        }

        // return $this->response->withArray(['user' => $user]);
        return $this->item($user, new ProfileTransformer($user), ['key' => 'user']);

    }

    public function update(Request $request)
    {
        $me = $this->auth->user();
        $data = $request->all();
        if (isset($data['email'])) {
            unset($data['email']);
        }
        $is_driver = config('carpoolear.module_validated_drivers', false) && (isset($data['user_be_driver']) || $me->driver_is_verified);
        // var_dump($is_driver);die;
        $profile = $this->userLogic->update($me, $data, $is_driver);
        if (! $profile) {
            throw new UpdateResourceFailedException('Could not update user.', $this->userLogic->getErrors());
        }
        return $this->item($profile, new ProfileTransformer($me), ['key' => 'user']);
    }
    
    public function adminUpdate(Request $request) {
        \Log::info('update controller: acaaaaaaaaaa ........ ---------' );
        $me = $this->auth->user();
        $data = $request->all();
        if (isset($data['user'])) {
            $user = $data['user'];
            $user = $this->userLogic->show($me, $user['id']);
            unset($data['user']);
        }
        if ($me->is_admin) {
            \Log::info('update controller: ' . $user->name);
            $profile = $this->userLogic->update($user, $data, false, true);
            if (!$profile) {
                throw new UpdateResourceFailedException('Could not update user.', $this->userLogic->getErrors());
            }
        } 
        return $this->item($profile, new ProfileTransformer($user), ['key' => 'user']);
    }

    public function updatePhoto(Request $request)
    {
        $me = $this->auth->user();
        $profile = $this->userLogic->updatePhoto($me, $request->all());
        if (! $profile) {
            throw new  UpdateResourceFailedException('Could not update user.', $this->userLogic->getErrors());
        }

        return $this->item($profile, new ProfileTransformer($me), ['key' => 'user']);
    }

    public function show($id = null)
    {
        $me = $this->auth->user();
        if (!($id > 0)) {
            $id = $me->id;
        }
        $profile = $this->userLogic->show($me, $id);
        if (! $profile) {
            throw new ResourceException('Users not found.', $this->userLogic->getErrors());
        }

        return $this->item($profile, new ProfileTransformer($me), ['key' => 'user']);
    }

    public function index(Request $request)
    {
        $search_text = null;
        if ($request->has('value')) {
            $search_text = $request->get('value');
        }
        $users = $this->userLogic->index($this->user, $search_text);

        return $this->collection($users, new ProfileTransformer($this->user));
    }
    public function searchUsers (Request $request) {
        $search_text = null;
        if ($request->has('name')) {
            $search_text = $request->get('name');
        }
        $users = $this->userLogic->searchUsers($search_text);
        return $this->collection($users, new ProfileTransformer($this->user));
    }

    public function registerDonation(Request $request)
    {
        $donation = new Donation();
        if ($request->has('has_donated')) {
            $donation->has_donated = $request->get('has_donated');
        }
        if ($request->has('has_denied')) {
            $donation->has_denied = $request->get('has_denied');
        }
        if ($request->has('ammount')) {
            $donation->ammount = $request->get('ammount');
        }
        if ($request->has('trip_id')) {
            $donation->trip_id = $request->get('trip_id');
        }
        $user = null;
        if ($request->has('user')) {
            $user = new \stdClass();
            $user->id = intval($request->get('user'));
            if (! $user->id > 0) {
                $user->id = 164619; //donador anonimo
            }
        } else {
            $user = $this->user;
        }
        $donation = $this->userLogic->registerDonation($user, $donation);

        return $donation;
    }
    public function bankData(Request $request)
    {
        $data = $this->userLogic->bankData();

        return json_encode($data);
    }


    public function terms (Request $request)
    {
        $lang = $request->has('lang') ? $request->get('lang') : '';
        $data = $this->userLogic->termsText($lang);

        return json_encode($data);
    }

    public function changeBooleanProperty($property, $value, Request $request)
    {
        $user = $this->user;
        $user->$property = $value > 0;
        $user->save();
        $profile = $this->userLogic->show($user, $user->id);

        return $this->item($profile, new ProfileTransformer($user), ['key' => 'user']);

    }
}
