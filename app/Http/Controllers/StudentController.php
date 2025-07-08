<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function studDashboard()
    {
        $studentId = session('id');
        $firstname = session('firstname', 'Guest');
        $lastname = session('lastname', '');
        $role = session('role', 'Guest');
        $yearLevel = session('yearLevel');
        $block = session('block');
        $student_id = session('student_id');
    
        $profile = DB::table('avatar')
        ->where('student_id', session('student_id'))
        ->select('profile')
        ->first();
    
        $totalExpenses = DB::table('expenses')->sum('amount');
    
        $studentBalance = DB::table('createpayable')
            ->where('student_id', $student_id)
            ->where('yearLevel', $yearLevel)
            ->where('block', $block)
            ->sum('amount');
  
        $totalPaid = DB::table('remittance')
            ->where('student_id', $student_id)
            ->whereIn('status', ['REMITTED', 'TO TREASURER', 'COLLECTED', 'COLLECTED BY TREASURER'])
            ->sum('paid');
    
        return view('student.studDashboard', compact(
            'firstname', 'lastname', 'role', 'profile', 
            'totalExpenses', 'studentBalance', 'totalPaid'
        ));
    }
    
    public function studLedger()
    {
        $student = DB::table('createuser')
            ->where('student_id', session('student_id')) 
            ->first();
    

        if (!$student) {
            return redirect()->route('student.notfound')->with('error', 'Student not found!');
        }
    
        $payables = DB::table('createpayable')
            ->where('student_id', session('student_id'))  
            ->select('description', DB::raw('COALESCE(SUM(amount), 0) as total_balance'))
            ->groupBy('description')
            ->get();
    
        $settledPayables = DB::table('remittance')
            ->where('student_id', session('student_id'))  
            ->select('date', 'description', 'paid', 'collectedBy', 'status')
            ->orderBy('date', 'asc')
            ->get();
    
            $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            // dd($profile);
    
        $firstname = session('firstname');
        $lastname = session('lastname');
    
        return view('Student.studLedger', compact('student', 'payables', 'settledPayables', 'profile', 'firstname', 'lastname'));
    }
    
    public function studPayableManagement() {
        $yearLevel = session('yearLevel');
        $block = session('block');
        $studentName = session('firstname') . ' ' . session('lastname');
    
        $Payables = DB::table('createpayable')
            ->select(
                'description', 
                'dueDate', 
                'balance as input_balance', 
                DB::raw('COUNT(id) as student_count'),
                DB::raw('(balance * COUNT(id)) as expected_receivable')
            )
            ->where('studentName', $studentName)  
            ->where('yearLevel', $yearLevel)      
            ->where('block', $block)            
            ->groupBy('description', 'dueDate', 'balance')
            ->get();
    
        $firstname = session('firstname');
        $lastname = session('lastname');
    
        $profile = DB::table('avatar')
        ->where('student_id', session('student_id'))
        ->select('profile')
        ->first();

        return view('student/studPayableManagement', compact('Payables', 'firstname', 'lastname', 'profile'));
    }
    public function studExpense()
{
    $availableDescriptions = DB::table('available_description')
        ->select('description', 'total_amount_collected')
        ->get();

    $paidData = [];
    foreach ($availableDescriptions as $item) {
        $paidData[$item->description] = $item->total_amount_collected;
    }

    $expenses = DB::table('expenses')
        ->select('description', 'quantity', 'label', 'price', 'amount', 'date', 'source')
        ->get();

    $groupedExpenses = $expenses->groupBy(function ($item) {
        return $item->date;
    });

    foreach ($groupedExpenses as $date => $expensesForDate) {
        $groupedExpenses[$date] = $expensesForDate->groupBy('source');
    }
    $sourcesByDate = [];
    foreach ($groupedExpenses as $date => $expensesForDate) {
        $sourcesByDate[$date] = array_keys($expensesForDate->toArray()); 
    }
     $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

    $firstname = session('firstname');
    $lastname = session('lastname');

    return view('student.studExpense', compact('firstname', 'lastname', 'paidData', 'groupedExpenses', 'profile', 'sourcesByDate') + [
        'descriptions' => $availableDescriptions->pluck('description'),
    ]);
}
public function getStudExpensesByDateAndSource($date, $source)
{
    
    $expenses = DB::table('expenses')
        ->whereDate('date', $date)
        ->where('source', $source)
        ->get(['description', 'amount']);
    
    return response()->json($expenses); 
}

    
    
    
    public function studUserDetails()
    {
        $role = session('role', 'Guest');
        $id = session('id', '');
        $student_id = session('student_id', '');
        $firstname = session('firstname', '');
        $lastname = session('lastname', '');
        $yearLevel = session('yearLevel', '');
        $block = session('block', '');
        $gender = session('gender', '');
        $username = session('username', '');
        $password = session('password', '');

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

        return view('Student.studUserDetails', compact('profile', 'student_id', 'firstname', 'lastname', 'role', 'yearLevel', 'block', 'username', 'password', 'gender'));
    }

    public function studChange(Request $request)
    {

        $userId = session('id', '');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'You need to be logged in.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:3|confirmed',
        ]);


        $user = DB::table('createuser')
            ->where('id', $userId)
            ->first();


        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }


        DB::table('createuser')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->new_password),
            ]);


        return back()->with('success', 'Password changed successfully.');
    }

    

   
}
