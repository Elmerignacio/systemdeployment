<?php

use App\Http\Controllers\ProfileController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TreasurerController;

use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\AdminController;


Route::get('/', [loginController::class, 'showLoginForm'])->name('login');
Route::post('/', [loginController::class, 'authenticate']);
Route::post('/logout', [loginController::class, 'logout'])->name('logout');

//TREASURER
Route::middleware('TREASURER')->group(function () {
   
    Route::post('/treasurer/saveUserImage', [TreasurerController::class, 'saveUserImage'])->name('saveUserImage');
    Route::get('/treasurer/expense', [TreasurerController::class, 'expense']);
    Route::get('/treasurer/dashboard', [TreasurerController::class, 'dashboard'])->name('dashboard');
    Route::get('/treasurer/manageUser', [TreasurerController::class, 'Manageuser']);
    Route::get('/treasurer/payableManagement', [TreasurerController::class, 'Payablemanagement']);
    Route::get('/treasurer/createPayable', [TreasurerController::class, 'Createpayable']);
    Route::get('/treasurer/studentBalance', [TreasurerController::class, 'Studentbalance']);
    Route::get('/treasurer/collection', [TreasurerController::class, 'Collection']);
    Route::get('/treasurer/get-students-and-blocks', [TreasurerController::class, 'getStudentsAndBlocks']);
    Route::post('/treasurer/saveData', [TreasurerController::class, 'saveuser']);
    Route::post('/treasurer/savePayable', [TreasurerController::class, 'savepayable']);
    Route::get('/treasurer/student-ledger/{id}', [TreasurerController::class, 'showLedger'])->name('student.ledger');
    Route::post('/treasurer/save-payment', [TreasurerController::class, 'SavePayment'])->name('treasave.payment');
    Route::get('/treasurer/remitted', [TreasurerController::class, 'Remitted']);
    Route::get('/treasurer/userDetails', [TreasurerController::class, 'userDetails']);
    Route::get('/treasurer/get-user-info', [TreasurerController::class, 'getUserInfo']);
    Route::get('/get-user-info', [TreasurerController::class, 'getUserInfo']);
    Route::get('/treasurer/get-denomination', [TreasurerController::class, 'getDenomination']);
    Route::post('/treasurer/denomination', [TreasurerController::class, 'storedenomination'])->name('store.denomination');
    Route::post('/treasurer/update-remittance-status', [TreasurerController::class, 'updateRemittanceStatus']);
    Route::post('/treasurer/update-user', [TreasurerController::class, 'update'])->name('user.update');
    Route::post('/treasurer/disburse/store', [TreasurerController::class, 'storeExpense'])->name('expenses.store');
    Route::put('/treasurer/change-password', [TreasurerController::class, 'change'])->name('password.change');
    Route::get('/treasurer/get-student-payables/{studentId}', [TreasurerController::class, 'getStudentPayables']);
    Route::get('/treasurer/get-expenses/{date}/{source}', [TreasurerController::class, 'getExpensesByDateAndSource']);
    Route::get('/treasurer/report', [TreasurerController::class, 'report']);
    Route::get('/treasurer/fund', [TreasurerController::class, 'fund']);
    Route::delete('/payable/delete/{description}', [TreasurerController::class, 'deletePayable']);
    Route::get('/treasurer/archiveUser', [TreasurerController::class, 'ArchiveUser']);  
    Route::put('/payable/update/{encodedDescription}', [TreasurerController::class, 'update'])->name('payable.update');
    Route::get('/treasurer/CashOnHand', [TreasurerController::class, 'CashOnHand']);
 


});


//OPEN FOR ALL 
    Route::post('/change/profile', [ProfileController::class, 'store'])->name('image.upload');
    Route::post('/users/modify', [TreasurerController::class, 'modifyUser'])->name('modify.users');
    Route::get('/treasurer/remitted/students', [TreasurerController::class, 'getStudentsWhoPaid']);
    Route::post('/treasurer/archive-users', [TreasurerController::class, 'archiveUsers'])->name('archive.users');
   





//representative
Route::middleware('REPRESENTATIVE')->group(function () {
    Route::get('/representative/dashboard', [RepresentativeController::class, 'RepDashboard'])->name('repdashboard');
    Route::get('representative/collection', [RepresentativeController::class, 'repCollection']);
    Route::get('representative/remitted', [RepresentativeController::class, 'RepRemitted']);
    Route::get('representative/CashOnHand', [RepresentativeController::class, 'RepCashOnHand']);
    Route::post('/denomination', [RepresentativeController::class, 'denomination'])->name('denomination.store');
    Route::post('/representative/save-payment', [RepresentativeController::class, 'RepSavePayment'])->name('repre.payment');
    Route::get('/representative/remitted/students', [RepresentativeController::class, 'getStudents']);
    Route::get('/representative/payableManagement', [RepresentativeController::class, 'repPayableManagement']);
    Route::get('/representative/userDetails', [RepresentativeController::class, 'RepUserDetails']);
    Route::put('/representative/change-password', [RepresentativeController::class, 'RepChange'])->name('password.Repchange');
    Route::get('/representative/get-student-payables/{studentId}', [RepresentativeController::class, 'RepStudentPayables']);
    Route::get('/representative/studentBalance', [RepresentativeController::class, 'RepStudentbalance']);
    Route::get('/representative/student-ledger/{id}', [RepresentativeController::class, 'RepShowLedger'])->name('RepStudent.ledger');
    Route::get('/representative/expense', [RepresentativeController::class, 'RepExpense']);
    Route::get('/representative/get-expenses/{date}/{source}', [RepresentativeController::class, 'getRepExpensesByDateAndSource']);




});




//Admin
Route::middleware('ADMIN')->group(function () {
    Route::post('/admin/saveUserImage', [AdminController::class, 'saveUserImage'])->name('saveUserImage');
    Route::get('/admin/expense', [AdminController::class, 'AdExpense']);
    Route::get('/admin/remitted', [AdminController::class, 'Remitted']);
    Route::get('/admin/dashboard', [AdminController::class, 'AdDashboard'])->name('AdminDashboard');
    Route::get('/admin/manageUser', [AdminController::class, 'AdManageUser']);
    Route::get('/admin/payableManagement', [AdminController::class, 'AdPayableManagement']);
    Route::get('/admin/createPayable', [AdminController::class, 'Createpayable']);
    Route::get('/admin/studentBalance', [AdminController::class, 'AdStudentBalance']);
    Route::get('/admin/archiveUser', [AdminController::class, 'AdArchiveUser']);
    Route::get('/admin/get-students-and-blocks', [AdminController::class, 'getStudentsAndBlocks']);
    Route::post('/admin/savePayable', [AdminController::class, 'savepayable']);
    Route::post('/admin/archive-users', [AdminController::class, 'archiveUsers'])->name('archive.userss');
    Route::get('/admin/student-ledger/{id}', [AdminController::class, 'AdStudentLedger'])->name('student.ledger');
    Route::get('/admin/remitted/students', [AdminController::class, 'getStudentsWhoPaid']);
    Route::get('/admin/CashOnHand', [AdminController::class, 'CashOnHand']);
    Route::get('/admin/userDetails', [AdminController::class, 'AdUserDetails']);
    Route::get('/admin/get-user-info', [AdminController::class, 'getAdUserInfo']);
    Route::post('/admin/update-remittance-status', [AdminController::class, 'updateRemittanceStatus']);
    Route::post('/admin/update-user', [AdminController::class, 'update'])->name('user.update');
    Route::post('/admin/disburse/store', [AdminController::class, 'storeExpense'])->name('AdExpenses.store');
    Route::put('/admin/change-password', [AdminController::class, 'Adchange'])->name('Adpassword.change');
    Route::get('/admin/get-student-payables/{studentId}', [AdminController::class, 'getStudentPayables']);
    Route::get('/admin/get-expenses/{date}/{source}', [AdminController::class, 'getAdExpensesByDateAndSource']);
    Route::post('/admin/disburse/store', [AdminController::class, 'AddStoreExpense'])->name('Adexpenses.store');
    Route::post('/admin/saveData', [AdminController::class, 'AdsaveUser']);
    Route::get('/admin/report', [AdminController::class, 'AdReport']);
    Route::get('/admin/fund', [AdminController::class, 'AdFund']);
    Route::get('/admin/remitted', [AdminController::class, 'AdRemitted']);

    Route::get('/admin/CashOnHand', [AdminController::class, 'AdCashOnHand']);
    Route::get('/admin/remitted/students', [AdminController::class, 'fetchStudents']);
Route::post('/admin/remittance/approve', [AdminController::class, 'approve'])->name('admin.remittance.approve');







});

Route::middleware('STUDENT')->group(function () {
    Route::get('student/dashboard', [StudentController::class, 'studDashboard'])->name('StudentDashboard');
    Route::get('/student/userDetails', [StudentController::class, 'studUserDetails']);
    Route::put('/student/change-password', [StudentController::class, 'studChange'])->name('StudPassword.change');
    Route::get('/student/payableManagement', [StudentController::class, 'studPayableManagement']);
    Route::get('/student/ledgers', [StudentController::class, 'studLedger']);
    Route::get('/student/expense', [StudentController::class, 'studExpense']);
    Route::get('/student/get-expenses/{date}/{source}', [StudentController::class, 'getStudExpensesByDateAndSource']);
});



