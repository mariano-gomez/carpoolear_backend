<?php

$v1_path = 'STS\Http\Controllers\Api\v1\\';

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [], function ($api) use ($v1_path) {
    $api->post('login', $v1_path.'AuthController@login');
    $api->post('retoken', $v1_path.'AuthController@retoken');
    $api->get('config', $v1_path.'AuthController@getConfig');
    $api->post('logout', $v1_path.'AuthController@logout');
    $api->post('activate/{activation_token?}', $v1_path.'AuthController@active');
    $api->post('reset-password', $v1_path.'AuthController@reset');
    $api->post('change-password/{token?}', $v1_path.'AuthController@changePasswod');
    $api->post('log', $v1_path.'DebugController@log');

    $api->group(['prefix' => 'users'], function ($api) use ($v1_path) {
        $api->get('/ratings', $v1_path.'RatingController@ratings');
        $api->get('/ratings/pending', $v1_path.'RatingController@pendingRate');
        $api->get('/get-trips', $v1_path.'TripController@getTrips');
        $api->get('/get-old-trips', $v1_path.'TripController@getOldTrips');
        $api->get('/my-trips', $v1_path.'TripController@getTrips');
        $api->get('/my-old-trips', $v1_path.'TripController@getOldTrips');
        $api->get('/requests', $v1_path.'PassengerController@allRequests');
        $api->get('/payment-pending', $v1_path.'PassengerController@paymentPendingRequest');

        $api->get('/list', $v1_path.'UserController@index');
        $api->get('/search', $v1_path.'UserController@searchUsers');

        $api->post('/', $v1_path.'UserController@create');
        $api->get('/me', $v1_path.'UserController@show');
        $api->get('/bank-data', $v1_path.'UserController@bankData');
        $api->get('/terms', $v1_path.'UserController@terms');
        $api->get('/{name?}', $v1_path.'UserController@show');
        $api->get('/{id?}/ratings', $v1_path.'RatingController@ratings');
        $api->put('/', $v1_path.'UserController@update');
        $api->put('/modify', $v1_path.'UserController@adminUpdate');
        $api->put('/photo', $v1_path.'UserController@updatePhoto');
        $api->post('/donation', $v1_path.'UserController@registerDonation');
        $api->any('/change/{property?}/{value?}', $v1_path.'UserController@changeBooleanProperty');
    });

    $api->group(['prefix' => 'notifications'], function ($api) use ($v1_path) {
        $api->get('/', $v1_path.'NotificationController@index');
        $api->delete('/{id?}', $v1_path.'NotificationController@delete');
        $api->get('/count', $v1_path.'NotificationController@count');
    });

    $api->group(['prefix' => 'friends'], function ($api) use ($v1_path) {
        $api->post('/accept/{id?}', $v1_path.'FriendsController@accept');
        $api->post('/request/{id?}', $v1_path.'FriendsController@request');
        $api->post('/delete/{id?}', $v1_path.'FriendsController@delete');
        $api->post('/reject/{id?}', $v1_path.'FriendsController@reject');
        $api->get('/', $v1_path.'FriendsController@index');
        $api->get('/pedings', $v1_path.'FriendsController@pedings');
    });

    $api->group(['prefix' => 'social'], function ($api) use ($v1_path) {
        $api->post('/login/{provider?}', $v1_path.'SocialController@login');
        $api->post('/friends/{provider?}', $v1_path.'SocialController@friends');
        $api->put('/update/{provider?}', $v1_path.'SocialController@update');
    });

    $api->group(['prefix' => 'trips'], function ($api) use ($v1_path) {
        $api->get('/requests', $v1_path.'PassengerController@allRequests');

        $api->get('/transactions', $v1_path.'PassengerController@transactions');
        $api->get('/autocomplete', $v1_path.'RoutesController@autocomplete');
        $api->get('/', $v1_path.'TripController@search');
        $api->post('/', $v1_path.'TripController@create');
        $api->put('/{id?}', $v1_path.'TripController@update');
        $api->delete('/{id?}', $v1_path.'TripController@delete');
        $api->get('/{id?}', $v1_path.'TripController@show');
        $api->post('/{id?}/changeSeats', $v1_path.'TripController@changeTripSeats');
        $api->post('/{id}/change-visibility', $v1_path.'TripController@changeVisibility');
        $api->post('/price', $v1_path.'TripController@price');
        
        $api->get('/{tripId}/passengers', $v1_path.'PassengerController@passengers');
        $api->get('/{tripId}/requests', $v1_path.'PassengerController@requests');

        $api->post('/{tripId}/requests', $v1_path.'PassengerController@newRequest');
        $api->post('/{tripId}/requests/{userId}/cancel', $v1_path.'PassengerController@cancelRequest');
        $api->post('/{tripId}/requests/{userId}/accept', $v1_path.'PassengerController@acceptRequest');
        $api->post('/{tripId}/requests/{userId}/reject', $v1_path.'PassengerController@rejectRequest');
        $api->post('/{tripId}/requests/{userId}/pay', $v1_path.'PassengerController@payRequest');

        $api->post('/{tripId}/rate/{userId}', $v1_path.'RatingController@rate');
        $api->post('/{tripId}/reply/{userId}', $v1_path.'RatingController@replay');
    });

    $api->group(['prefix' => 'conversations'], function ($api) use ($v1_path) {
        $api->get('/', $v1_path.'ConversationController@index');
        $api->post('/', $v1_path.'ConversationController@create');
        $api->get('/user-list', $v1_path.'ConversationController@userList');
        $api->get('/unread', $v1_path.'ConversationController@getMessagesUnread');
        $api->get('/show/{id?}', $v1_path.'ConversationController@show');

        $api->get('/{id?}', $v1_path.'ConversationController@getConversation');
        $api->get('/{id?}/users', $v1_path.'ConversationController@users');
        $api->post('/{id?}/users', $v1_path.'ConversationController@addUser');
        $api->delete('/{id?}/users/{userId?}', $v1_path.'ConversationController@deleteUser');
        $api->post('/{id?}/send', $v1_path.'ConversationController@send');
        $api->post('/multi-send', $v1_path.'ConversationController@multiSend');
    });

    $api->group(['prefix' => 'cars'], function ($api) use ($v1_path) {
        $api->get('/', $v1_path.'CarController@index');
        $api->post('/', $v1_path.'CarController@create');
        $api->put('/{id?}', $v1_path.'CarController@update');
        $api->delete('/{id?}', $v1_path.'CarController@delete');
        $api->get('/{id?}', $v1_path.'CarController@show');
    });

    $api->group(['prefix' => 'subscriptions'], function ($api) use ($v1_path) {
        $api->get('/', $v1_path.'SubscriptionController@index');
        $api->post('/', $v1_path.'SubscriptionController@create');
        $api->put('/{id?}', $v1_path.'SubscriptionController@update');
        $api->delete('/{id?}', $v1_path.'SubscriptionController@delete');
        $api->get('/{id?}', $v1_path.'SubscriptionController@show');
    });

    $api->group(['prefix' => 'devices'], function ($api) use ($v1_path) {
        $api->get('/', $v1_path.'DeviceController@index');
        $api->post('/', $v1_path.'DeviceController@register');
        $api->put('/{id?}', $v1_path.'DeviceController@update');
        $api->delete('/{id?}', $v1_path.'DeviceController@delete');
    });
    $api->group(['prefix' => 'data'], function ($api) use ($v1_path) {
        $api->get('/trips', $v1_path.'DataController@trips');
        $api->get('/seats', $v1_path.'DataController@seats');
        $api->get('/users', $v1_path.'DataController@users');
        $api->get('/monthlyusers', $v1_path.'DataController@monthlyUsers');
    });
    $api->group(['prefix' => 'references'], function ($api) use ($v1_path) {
        $api->post('/', $v1_path.'ReferencesController@create');
    });
});