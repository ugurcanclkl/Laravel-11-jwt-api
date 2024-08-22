<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\RoomUserController;
use App\Http\Controllers\Api\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Api\Admin\RoomUserController as AdminRoomUserController;
use App\Http\Controllers\Api\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->name('api')->group(function (): void {
    Route::group(['prefix' => '/auth', 'as' => '.auth'], function (): void {
        Route::post('/login', LoginController::class)->withoutMiddleware('auth:api')->name('.login');
        Route::post('/register', RegisterController::class)->withoutMiddleware('auth:api')->name('.register');
        Route::post('/logout', LogoutController::class)->name('.logout');
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('.user');
    });

    Route::group(['prefix' => 'rooms', 'as' => '.rooms'], function (): void {
        Route::get('/', [RoomController::class, 'index'])->middleware('permission:list-room')->name('.list');
        
        Route::middleware('role:admin|user')->group(function (): void {
            Route::post('/{room:id}', [RoomController::class, 'join'])->middleware('permission:view-room')->name('.join');
            Route::put('/{room:id}', [RoomController::class, 'cancel'])->middleware('permission:view-room')->name('.cancel');

            Route::group(['prefix' => 'messages', 'as' => '.messages'], function (): void {
                Route::get('/{assignedRoom}', [MessageController::class, 'index'])->middleware('permission:view-message')->name('.index');
                Route::post('/{assignedRoom}', [MessageController::class, 'send'])->middleware('permission:create-message')->name('.store');
                Route::put('/{assignedRoom}/{userMessage}', [MessageController::class, 'update'])->middleware('permission:edit-message')->name('.update');
                Route::delete('/{assignedRoom}/{userMessage}', [MessageController::class, 'delete'])->middleware('permission:delete-message')->name('.delete');
            });

            Route::group(['prefix' => 'roomUsers', 'as' => '.roomUsers'], function (): void {
                Route::get('/', [RoomUserController::class, 'index'])->middleware('permission:list-roomUser')->name('.list');
                Route::get('/{ownedRoomUser}', [RoomUserController::class, 'show'])->middleware('permission:view-roomUser')->name('.show');
                Route::get('/room/{room:id}', [RoomUserController::class, 'getByRoomId'])->middleware('permission:view-roomUser')->name('.getByRoomId');
            });
        });
    });

    Route::middleware(['role:admin'])->group(function (): void {
        Route::group(['prefix' => 'admin', 'as' => '.admin'], function (): void {
            Route::group(['prefix' => 'rooms', 'as' => '.rooms'], function (): void {
                Route::get('/', [AdminRoomController::class, 'adminIndex'])->middleware('permission:list-room')->name('.list');
                Route::post('/', [AdminRoomController::class, 'adminCreate'])->middleware('permission:create-room')->name('.store');
                Route::get('/{room:id}', [AdminRoomController::class, 'adminShow'])->middleware('permission:view-room')->name('.show');
                Route::put('/{room:id}', [AdminRoomController::class, 'adminUpdate'])->middleware('permission:edit-room')->name('.update');
                Route::delete('/{room:id}', [AdminRoomController::class, 'adminDestroy'])->middleware('permission:delete-room')->name('.destroy');
            });

            Route::group(['prefix' => 'messages', 'as' => '.messages'], function (): void {
                Route::get('/', [AdminMessageController::class, 'adminIndex'])->middleware(['permission:list-message'])->name('.list');
                Route::post('/', [AdminMessageController::class, 'adminCreate'])->middleware('permission:create-room')->name('.store');
                Route::get('/{message:id}', [AdminMessageController::class, 'adminShow'])->middleware('permission:view-message')->name('.show');
                Route::put('/{message:id}', [AdminMessageController::class, 'adminUpdate'])->middleware('permission:edit-room')->name('.update');
                Route::delete('/{message:id}', [AdminMessageController::class, 'adminDelete'])->middleware('permission:delete-message')->name('.destroy');
            });

            Route::group(['prefix' => 'userRooms', 'as' => '.userRooms'], function (): void {
                Route::get('/', [AdminRoomUserController::class, 'adminIndex'])->middleware(['permission:list-userRoom'])->name('.list');
                Route::get('/{userRoom:id}', [AdminRoomUserController::class, 'adminShow'])->middleware('permission:view-userRoom')->name('.show');
                Route::put('/{userRoom:id}', [AdminRoomUserController::class, 'adminUpdate'])->middleware('permission:edit-userRoom')->name('.update');
                Route::delete('/{userRoom:id}', [AdminRoomUserController::class, 'adminDelete'])->middleware('permission:delete-userRoom')->name('.destroy');
            });
        });
    });
});