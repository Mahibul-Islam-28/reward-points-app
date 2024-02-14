<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ReactController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ScoreController;

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminReportController;
use App\Http\Controllers\admin\UserManageController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//=================================
//========= User Panel ============
//=================================

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'logData']);

Route::get('/forgot_password', [LoginController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('/forgot_password', [LoginController::class, 'sendPassword']);

Route::get('/change_passoword', [LoginController::class, 'changePassowrd'])->name('changePassword');
Route::post('/change_passoword', [LoginController::class, 'savePassword']);

Route::get('/registration', [LoginController::class, 'registration'])->name('registration');
Route::post('/registration', [LoginController::class, 'store']);

Route::get('/verify_email', [LoginController::class, 'verifyEmail'])->name('verifyEmail');
Route::get('/change_email', [SettingController::class, 'changeEmail'])->name('changeEmail');

Route::get('/verify_phone', [LoginController::class, 'verifyPhone'])->name('verifyPhone');
Route::get('/otpVerifyUrl/{id}', [LoginController::class, 'otpVerifyUrl'])->name('otpVerifyUrl');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms-privacy', [PageController::class, 'termsPrivacy'])->name('termsPrivacy');
Route::get('/app-download', [PageController::class, 'download'])->name('appDownload');
Route::get('/how-it-works', [PageController::class, 'works'])->name('howWorks');
//Route::get('/eula', [PageController::class, 'eula'])->name('eula');

// Single Activity
Route::get('/activity/{id}', [ActivityController::class, 'singleActivity'])->name('singleActivity');

// Profile
Route::get('/profile/{userName}', [ProfileController::class, 'profile'])->name('profile');
Route::get('/profile/{userName}/ixprez', [ProfileController::class, 'ixprez'])->name('profileIxprez');

Route::get('/link', function () {
     return view('link');
});
Route::get('/guest', function () {
     return view('guest');
});

// Access Denied without Login
Route::middleware(['userLogin'])->group(function () {
     Route::get('/404', [PageController::class, 'notFound'])->name('404');

     Route::get('/', [IndexController::class, 'index'])->name('index');

     Route::get('/profile/{userName}/wexprez', [ProfileController::class, 'wexprez'])->name('profileWexprez');
     Route::get('/profile/{userName}/following', [ProfileController::class, 'following'])->name('profileFollowing');
     Route::get('/profile/{userName}/follower', [ProfileController::class, 'follower'])->name('profileFollower');
     Route::get('/profile/{userName}/archive', [ProfileController::class, 'archive'])->name('profileArchive');

     Route::get('/profile/{userName}/edit', [ProfileController::class, 'edit'])->name('profileEdit');
     Route::post('/profile/{userName}/edit', [ProfileController::class, 'update']);

     Route::get('/profile/{userName}/image', [ProfileController::class, 'profileImage'])->name('profileImage');
     Route::post('/profile/{userName}/image', [ProfileController::class, 'profileImageStore'])->name('profileImageUpload');

     Route::get('/profile/{userName}/cover', [ProfileController::class, 'coverImage'])->name('coverImage');
     Route::post('/profile/{userName}/cover', [ProfileController::class, 'coverImageStore'])->name('coverImageUpload');

     Route::get('/xprezers', [MemberController::class, 'index'])->name('member');
     Route::get('/followers', [MemberController::class, 'followers'])->name('followers');
     Route::get('/following', [MemberController::class, 'following'])->name('following');
     Route::get('/block-list', [MemberController::class, 'block'])->name('blockList');
     
     Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

     // Route::get('/wexprez', [ActivityController::class, 'wexprez'])->name('wexprez');

     Route::get('/logout', 'LoginController@logout')->name('logout');

     Route::get('/search/{value}', [SearchController::class, 'search'])->name('search');
     Route::get('/search/{value}/xprezers', [SearchController::class, 'xprezer'])->name('xprezerSearch');
     Route::get('/search/{value}/activity', [SearchController::class, 'activity'])->name('activitySearch');
     Route::get('/search/{value}/comment', [SearchController::class, 'comment'])->name('commentSearch');

     Route::get('/notification', [NotificationController::class, 'index'])->name('notification');
     Route::get('/notification/read/{id}', [NotificationController::class, 'readPage'])->name('notificationReadPage');

     Route::get('/settings', [SettingController::class, 'password'])->name('setting');
     Route::post('/settings', [SettingController::class, 'passwordChange']);

     Route::get('/settings/email', [SettingController::class, 'email'])->name('settingEmail');
     Route::post('/settings/email', [SettingController::class, 'sendEmail']);

     // Ajax
     Route::post('/activityStore', [ActivityController::class, 'activityStore'])->name('activityStore');

     Route::get('/activityEdit', [ActivityController::class, 'edit'])->name('activityEdit');
     Route::post('/activityUpdate', [ActivityController::class, 'update'])->name('activityUpdate');

     Route::get('/activityHide', [ActivityController::class, 'hide'])->name('activityHide');
     Route::get('/activityShow', [ActivityController::class, 'show'])->name('activityShow');

     Route::get('/activityDelete', [ActivityController::class, 'delete'])->name('activityDelete');
     Route::get('/imageDelete', [ActivityController::class, 'imageDelete'])->name('imageDelete');

     Route::get('/activityFilter', [ActivityController::class, 'filter'])->name('activityFilter');
     Route::get('/dateFilter', [ActivityController::class, 'dateFilter'])->name('dateFilter');

     Route::get('/activityShare', [ActivityController::class, 'activityShare'])->name('activityShare');

     Route::get('/linkPreview', [ActivityController::class, 'link'])->name('linkPreview');

     Route::get('/comment', [CommentController::class, 'comment'])->name('comment');
     Route::post('/saveComment', [CommentController::class, 'save'])->name('commentSave');

     Route::get('/editComment', [CommentController::class, 'edit'])->name('commentEdit');
     Route::post('/updateComment', [CommentController::class, 'update'])->name('commentUpdate');

     Route::get('/commentDelete', [CommentController::class, 'delete'])->name('commentDelete');

     Route::get('/follow', [FollowController::class, 'follow'])->name('follow');

     Route::get('/block', [BlockController::class, 'block'])->name('block');
     Route::get('/unblock', [BlockController::class, 'unblock'])->name('unblock');

     Route::get('/report', [ReportController::class, 'memberReport'])->name('memberReport');
     Route::post('/report', [ReportController::class, 'memberReportSave'])->name('memberReportSave');

     Route::get('/activityReport', [ReportController::class, 'activityReport'])->name('activityReport');
     Route::post('/activityReportSave', [ReportController::class, 'activityReportSave'])->name('activityReportSave');

     Route::get('/commentReport', [ReportController::class, 'commentReport'])->name('commentReport');
     Route::post('/commentReportSave', [ReportController::class, 'commentReportSave'])->name('commentReportSave');

     Route::get('/voteUp', [VoteController::class, 'voteUp'])->name('activityVoteUp');
     Route::get('/voteDown', [VoteController::class, 'voteDown'])->name('activityVoteDown');
     Route::get('/voteUpList', [VoteController::class, 'voteUpList'])->name('activityVoteUpList');
     Route::get('/voteDownList', [VoteController::class, 'voteDownList'])->name('activityVoteDownList');

     Route::get('/real', [ReactController::class, 'real'])->name('activityReal');
     Route::get('/fake', [ReactController::class, 'fake'])->name('activityFake');
     Route::get('/realList', [ReactController::class, 'realList'])->name('activityRealList');
     Route::get('/fakeList', [ReactController::class, 'fakeList'])->name('activityFakeList');

     Route::get('/commentVoteUp', [VoteController::class, 'commentVoteUp'])->name('commentVoteUp');
     Route::get('/commentVoteDown', [VoteController::class, 'commentVoteDown'])->name('commentVoteDown');
     Route::get('/commentVoteUpList', [VoteController::class, 'commentVoteUpList'])->name('commentVoteUpList');
     Route::get('/commentVoteDownList', [VoteController::class, 'commentVoteDownList'])->name('commentVoteDownList');

     Route::get('/commentReal', [ReactController::class, 'commentReal'])->name('commentReal');
     Route::get('/commentFake', [ReactController::class, 'commentFake'])->name('commentFake');
     Route::get('/commentRealList', [ReactController::class, 'commentRealList'])->name('commentRealList');
     Route::get('/commentFakeList', [ReactController::class, 'commentFakeList'])->name('commentFakeList');

     Route::get('/activityMention', [ActivityController::class, 'mention'])->name('activityMention');
     Route::get('/activityAddMention', [ActivityController::class, 'addMention'])->name('activityAddMention');

     Route::get('/memberVoteUp', [MemberController::class, 'voteUp'])->name('memberVoteUp');
     Route::get('/memberVoteDown', [MemberController::class, 'voteDown'])->name('memberVoteDown');

     Route::get('/memberVoteUpList', [MemberController::class, 'voteUpList'])->name('memberVoteUpList');
     Route::get('/memberVoteDownList', [MemberController::class, 'voteDownList'])->name('memberVoteDownList');

     Route::get('/memberSearch', [MemberController::class, 'search'])->name('memberSearch');
     Route::get('/followerSearch', [MemberController::class, 'followerSearch'])->name('followerSearch');
     Route::get('/followingSearch', [MemberController::class, 'followingSearch'])->name('followingSearch');
     Route::get('/blockSearch', [MemberController::class, 'blockSearch'])->name('blockSearch');

     Route::get('/notificationList', [NotificationController::class, 'list'])->name('notificationList');
     Route::get('/notificationRead', [NotificationController::class, 'read'])->name('notificationRead');

     Route::get('/otpVerify', [LoginController::class, 'otpVerify'])->name('otpVerify');

     Route::get('/scoreDetails', [ScoreController::class, 'details'])->name('scoreDetails');


});

//=================================
//========= Admin Panel ===========
//=================================
Route::group(['prefix' => 'wxadmin'], function () {

     Route::get('login', [AdminLoginController::class, 'login'])->name('adminLogin');
     Route::post('login', [AdminLoginController::class, 'logData']);

     Route::middleware(['adminLogin'])->group(function () {

     Route::get('/logout', [AdminLoginController::class, 'logout'])->name('adminLogout');

     Route::get('', [AdminController::class, 'dashboard'])->name('dashboard');

     Route::get('userManage', [UserManageController::class, 'index'])->name('userManage');

     Route::get('deletedUser', [UserManageController::class, 'deletedUser'])->name('deletedUser');

     Route::get('report/userList', [AdminReportController::class, 'userList'])->name('userReportList');
     Route::get('report/activityList', [AdminReportController::class, 'activityList'])->name('activityReportList');
     Route::get('report/commentList', [AdminReportController::class, 'commentList'])->name('commentReportList');

     Route::get('notification/create', [AdminController::class, 'notifyCreate'])->name('notificationCreate');
     Route::post('notification/create', [AdminController::class, 'notifyStore']);


     // ajax
     Route::get('report/temporaryBan', [UserManageController::class, 'temporaryBan'])->name('temporaryBan');
     Route::get('report/permanentBan', [UserManageController::class, 'permanentBan'])->name('permanentBan');
     Route::get('report/unban', [UserManageController::class, 'unban'])->name('unban');

     Route::get('report/viewActivity', [UserManageController::class, 'viewActivity'])->name('viewActivity');
     Route::get('report/hideActivity', [UserManageController::class, 'hideActivity'])->name('hideActivity');
     Route::get('report/viewComment', [UserManageController::class, 'viewComment'])->name('viewComment');
     Route::get('report/hideComment', [UserManageController::class, 'hideComment'])->name('hideComment');

     Route::get('report/unbanUser', [UserManageController::class, 'unbanUser'])->name('unbanUser');

     });

});
