<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Atymic\Twitter\Twitter as TwitterContract;
use Illuminate\Http\JsonResponse;
use Atymic\Twitter\Facade\Twitter;
use Session;
use Redirect;
use Auth;
use Exception;
class TwitterController extends Controller
{
    //
    public function tweet(Request $request){
        try{
            $validated = $request->validate([
                'text' => 'required|max:255',
            ]);
            $tweet = Twitter::postTweet(['status' => $validated['text'], 'response_format' => 'json']);
            return Redirect::to('/home');
        }
        catch(Exception $e){
            dd($e);
        }
    }

    public  function login(){
        $token = Twitter::getRequestToken(route('callback'));
        if (isset($token['oauth_token_secret'])) {
            $url = Twitter::getAuthenticateUrl($token['oauth_token']);
            Session::put('oauth_state', 'start');
            Session::put('oauth_request_token', $token['oauth_token']);
            Session::put('oauth_request_token_secret', $token['oauth_token_secret']);
            return Redirect::to($url);
        }
        return Redirect::route('error');
    }


    public function logout(){
        Session::forget('access_token');
        Auth::logout();
        return Redirect::to('/')->with('alert-notice', 'You\'ve successfully logged out!');
    }


    public  function callback(Request $request){
        if (Session::has('oauth_request_token')) {
            $twitter = Twitter::usingCredentials(session('oauth_request_token'), session('oauth_request_token_secret'));
            $token = $twitter->getAccessToken(request('oauth_verifier'));
            if (!isset($token['oauth_token_secret'])) {
                return Redirect::route('error')->with('alert-error', 'We could not log you in on Twitter.');
            }
            // use new tokens
            $twitter = Twitter::usingCredentials($token['oauth_token'], $token['oauth_token_secret']);
            $credentials = $twitter->getCredentials();
            if (is_object($credentials) && !isset($credentials->error)) {
                $user = \App\Models\User::firstOrNew(['twitter_id' => $credentials->id]);
                $user->screen_name = $credentials->screen_name;
                $user->name=$credentials->name;
                $user->save();
                Auth::login($user,true); 
                Session::put('access_token', $token);
                return Redirect::to('/')->with('alert-notice', 'Congrats! You\'ve successfully signed in!');
            }
        }
    
        return Redirect::route('error')
                ->with('alert-error', 'Crab! Something went wrong while signing you up!');
    }

   

    public function home(){
        $timeLines  = Twitter::getHomeTimeline();
        // dd($timeLines);
        return view('home',['timeLines'=>$timeLines]);
    }

    public function like(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|max:255',
            ]);
            $rt = Twitter::postFavorite(['id'=>$validated['id']]);
            return response()->json(array(
					'status' =>1,
			), 200);
        }
        catch (Exception $e){
            return response()->json(array(
                'status' =>0,
                'msg' => $e->getMessage(),
            ), 200);
        }
       

    }

    public function unlike(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|max:255',
            ]);
            $rt = Twitter::destroyFavorite(['id'=>$validated['id']]);
            return response()->json(array(
                'status' =>1,
            ), 200);
        }
        catch (Exception $e){
            return response()->json(array(
                'status' =>0,
                'msg' => $e->getMessage(),
            ), 200);
        }
    }


}
