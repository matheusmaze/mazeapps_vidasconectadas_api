<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Middleware usuário ADMINISTRADOR
Route::group(['middleware' => ['jwt.verify.adm']], function(){
    //Rotas Dashboard
    Route::get('/dashboard/cards_geral', 'AdmController@cards_geral');
    Route::get('/dashboard/reports/{month}', 'AdmController@reportPerMonth');
    Route::get('/dashboard/users/{nivel}', 'UserController@getUserByNivel');

    // Rota de cadastro de usuário pelo ADM MASTER
    Route::post('/dashboard/register', 'UserController@registerByAdm');

    //Rotas Buys
    Route::get('/buys', 'BuyController@index');

    //Rotas Institutions
    Route::get('/institutions', 'InstitutionController@index');
    Route::get('/institutions/{id}', 'InstitutionController@show');
    Route::post('/institutions', 'InstitutionController@store');
    Route::post('/institutions/{id}', 'InstitutionController@update');
    Route::delete('/institutions/{id}', 'InstitutionController@destroy');

});


//Middleware para os usuário que fazer as doações(PADRINHO ou DOADOR)
Route::group(['middleware' => ['jwt.verify.padrinho']], function(){

    // Rota de Cadastro de usuário feito por um PAdrinho já existente
    Route::post('users/register', 'UserController@registerByPadrinho');
    Route::get('/users/{id}', 'UserController@show');
    Route::get('/users/users/{id}', 'UserController@getUserByUserId');
    Route::put('/users/{id}', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@destroy');
    Route::get('/users', 'UserController@index');
    Route::get('/users/dashboard/cards', 'UserController@cards_users');

    //Rota Donations
    Route::get('/donations/users/{id}', 'DonationController@getDonationByUserId');
    Route::get('/donations/{id}', 'DonationController@show');
    Route::put('/donations/{id}', 'DonationController@update');
    Route::delete('/donations/{id}', 'DonationController@destroy');
    Route::post('/donations/users/invited/{unique?}', 'DonationController@storeToInvited');
    Route::put('/donations/users/invited/{id}/{unique?}', 'DonationController@updateToInvited');
    Route::get('/donations', 'DonationController@index');

    //Rota transaction
    Route::get('/transactions/donations/{id}', 'TransactionController@getTransactionByDonationId');

});

//Middleware para os negociadores/compradores, onde ficarão as rotas para lançar o que foi comprado e para quem
Route::group(['middleware' => ['jwt.verify.negociador']], function(){
    //Rota Users
    Route::get('/users', 'UserController@index');
    //Rotas Institutions
    Route::get('/institutions', 'InstitutionController@index');
    //Rotas Buys
    Route::get('/buys/users/{id}', 'BuyController@getBuyByUserId');
    Route::get('/buys/{id}', 'BuyController@show');
    Route::post('/buys', 'BuyController@store');
    Route::post('/buys/{id}', 'BuyController@update');
    Route::delete('/buys/{id}', 'BuyController@destroy');
});

//Rota de Registro feito através do site
Route::post('/register', 'UserController@registerBySite');
//Rota de Login(único para todos os usuários)
Route::post('/login', 'UserController@authenticate');

//Rota de doação
Route::post('/donations/{unique?}', 'DonationController@store');
Route::put('/donations/{id}/{unique?}', 'DonationController@updateDonationUnique');

// Rotas transações
Route::get('/transactions', 'TransactionController@index');
Route::get('/transactions/{id}', 'TransactionController@show');
Route::post('/transactions', 'TransactionController@store');
Route::post('/transactions/{id}', 'TransactionController@update');
Route::delete('/transactions/{id}', 'TransactionController@destroy');

//Validação de Email
Route::get('/confirmar_email/{codigo}', 'EmailController@confirmar_email');
//Route::put('/enviar_confirmacao_email/{id}', 'EmailController@enviar_confirmacao_email');

//Resetar senha
Route::put('/reset_password', 'UserController@reset_password');

//Rota do site
Route::get('/dashboard/reports/year/{year}/{month}', 'SiteController@reportPerYear');
Route::get('/donations/filtered/{datainicio?}/{datafim?}', 'DonationController@indexFiltered');
