<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;



class RepresentativeController extends Controller
{
public function RepDashboard()
    {
        $firstname = session('firstname', 'Guest');
        $lastname = session('lastname', '');
        $role = session('role', 'Guest');

        $yearLevel = session('yearLevel');
        $block = session('block');

        $remittedTotal = DB::table('remittance')
            ->where('status', ['COLLECTED','TO TREASURER'])
            ->where('yearLevel', $yearLevel)
            ->where('block', $block)
            ->select(DB::raw('SUM(paid) as total'))
            ->value('total');

        $totalBalance = DB::table('createpayable')
            ->where('yearLevel', $yearLevel)
            ->where('block', $block)
            ->sum('balance');

            $Payables = DB::table('createpayable')
            ->select(
                'description',
                'dueDate',
                'balance as input_balance',
                DB::raw('COUNT(id) as student_count'),
                DB::raw('(balance * COUNT(id)) as expected_receivable')
            )
            ->groupBy('description', 'dueDate', 'balance')
            ->get();


        $profile = DB::table('avatar')
        ->where('student_id', session('student_id'))
        ->select('profile')
        ->first();

            $firstname = session('firstname', 'Guest');
            $lastname = session('lastname', '');

            $totalExpenses = DB::table('expenses')->sum('amount');


        return view('representative.repdashboard', compact('firstname', 'lastname', 'role', 'remittedTotal', 'totalBalance','Payables','profile','totalExpenses'));
    }

    public function RepUserDetails()
    {
        $role = session('role', 'Guest');
        $id = session('id', '');
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

        //    dd()



        return view('Representative.RepUserDetails', compact('profile', 'id', 'firstname', 'lastname', 'role', 'yearLevel', 'block', 'username', 'password', 'gender'));
    }

function repCollection() {
    $yearLevel = session('yearLevel');
    $block = session('block');

    $students = DB::table('createuser')
        ->whereIn('role', ['representative', 'student', 'treasurer'])
        ->where('yearLevel', $yearLevel)
        ->where('block', $block)
        ->orderBy('lastname', 'asc')
        ->get();


        $firstname = session('firstname');
        $lastname = session('lastname');



        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();


    return view('representative.repCollection', compact('students','profile','firstname','lastname'));
}


public function RepRemitted()
{
    $userYearLevel = session('yearLevel');
    $userBlock = session('block');

    $remittances = DB::table('remittance')
        ->leftJoin('createuser', function ($join) {
            $join->on('remittance.yearLevel', '=', 'createuser.yearLevel')
                 ->on('remittance.block', '=', 'createuser.block')
                 ->whereIn('createuser.role', ['TREASURER', 'REPRESENTATIVE']);
        })
        ->select(
            'remittance.*',
            'createuser.yearLevel as userYearLevel',
            'createuser.block as userBlock',
            'remittance.firstname',
            'remittance.lastname',
            'remittance.collectedBy'
        )
        ->where('remittance.yearLevel', $userYearLevel)
        ->where('remittance.block', $userBlock)
        ->orderBy('remittance.status', 'asc')
        ->get();

    $balances = DB::table('createpayable')
        ->select('balance', 'description', 'yearLevel', 'block')
        ->get();

        foreach ($remittances as $remittance) {
        $matchingAmount = $balances->firstWhere(function ($payable) use ($remittance) {
            return $payable->description === $remittance->description
                && $payable->yearLevel === $remittance->yearLevel
                && $payable->block === $remittance->block;
        });

        $remittance->balance = $matchingAmount ? $matchingAmount->balance : 0;
    }

    $paids = DB::table('remittance')
    ->select('paid', 'description', 'yearLevel', 'block', 'date', 'status', 'collectedBy')
    ->get();



    $collectors = DB::table('createuser')
        ->whereIn('role', ['TREASURER', 'REPRESENTATIVE'])
        ->select('firstname', 'lastname', 'role', 'yearLevel', 'block')
        ->get();


        $firstname = session('firstname');
        $lastname = session('lastname');




        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();



    return view('representative.repRemitted', compact('remittances', 'collectors', 'balances','paids','profile','firstname','lastname'));
}

public function RepCashOnHand()
{

    $userYearLevel = session('yearLevel');
    $userBlock = session('block');

    $remittances = DB::table('remittance')
        ->leftJoin('createuser', function ($join) {
            $join->on('remittance.yearLevel', '=', 'createuser.yearLevel')
                 ->on('remittance.block', '=', 'createuser.block')
                 ->on('remittance.role', '=', 'createuser.role')
                 ->whereIn('createuser.role', ['TREASURER', 'REPRESENTATIVE']);
        })


        ->select(
            'remittance.*',
            'createuser.yearLevel as userYearLevel',
            'createuser.block as userBlock',
            'remittance.firstname',
            'remittance.lastname',
            'remittance.collectedBy'
        )
        ->where('remittance.yearLevel', $userYearLevel)
        ->where('remittance.block', $userBlock)
        ->where('remittance.status', 'COLLECTED')
        ->orderBy('remittance.date', 'asc')
        ->get();

    $balances = DB::table('createpayable')
        ->select('balance', 'description', 'yearLevel', 'block')
        ->get();

    foreach ($remittances as $remittance) {
        $matchingAmount = $balances->firstWhere(function ($payable) use ($remittance) {
            return $payable->description === $remittance->description
                && $payable->yearLevel === $remittance->yearLevel
                && $payable->block === $remittance->block;
        });

        $remittance->balance = $matchingAmount ? $matchingAmount->balance : 0;
    }

    $paids = DB::table('remittance')
        ->select('paid', 'description', 'yearLevel', 'block', 'date','status')
        ->get();

    $groupedRemittances = $remittances->groupBy(function ($remittance) {
        return \Carbon\Carbon::parse($remittance->date)->format('Y-m-d') . '-' . $remittance->collectedBy;
    });

    // Calculate totalAmount
    $totalAmount = 0;
    foreach ($remittances as $remittance) {
        $totalPaid = $paids->where('description', $remittance->description)
                          ->where('yearLevel', $remittance->yearLevel)
                          ->where('block', $remittance->block)
                          ->where('date', $remittance->date)
                          ->sum('paid');
        $totalCollected = $remittance->totalAmount ?? 0;
        $totalAmount += $totalPaid + $totalCollected;
    }

    $collectors = DB::table('createuser')
        ->whereIn('role', ['TREASURER', 'REPRESENTATIVE'])
        ->select('firstname', 'lastname', 'role', 'yearLevel', 'block')
        ->get();

        $firstname = session('firstname');
        $lastname = session('lastname');




        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();




    return view('representative.repCashOnHand', compact('remittances', 'collectors', 'balances', 'paids', 'groupedRemittances', 'totalAmount','firstname', 'lastname', 'profile'));
}
public function RepSavePayment(Request $req)
{
    \Log::info('Request data:', $req->all());

    $studentId = $req->student_id;
    $payableIds = $req->payable_id;
    $amountsPaid = $req->amount_paid;
    $date = $req->date;

    if (!$studentId || !$payableIds || !$amountsPaid || !$date) {
        return back()->with('error', 'All fields are required.');
    }

    $payableIds = is_array($payableIds) ? $payableIds : [$payableIds];
    $amountsPaid = is_array($amountsPaid) ? $amountsPaid : [$amountsPaid];

    $role = session('role');
    $collectedBy = session('firstname') . ' ' . session('lastname');

    DB::beginTransaction();

    try {
        foreach ($payableIds as $index => $payableId) {
            $payable = DB::table('createpayable')->where('id', $payableId)->first();

            if ($payable) {
                $amountPaid = floatval($amountsPaid[$index] ?? 0);

                if ($amountPaid <= 0) {
                    continue;
                }

                if ($amountPaid > $payable->amount) {
                    return back()->with('error', 'Amount paid exceeds payable amount.');
                }

                $newBalance = $payable->amount - $amountPaid;

                DB::table('createpayable')->where('id', $payableId)->update([
                    'amount' => $newBalance
                ]);

                list($firstname, $lastname) = explode(' ', trim($payable->studentName), 2) + ['N/A', 'N/A'];

                $status = ($role == 'REPRESENTATIVE') ? 'COLLECTED' : 'COLLECTED BY TREASURER';

                \Log::info('Processing payment for: ' . $firstname . ' ' . $lastname . ' with status ' . $status);

                $existingPayment = DB::table('remittance')
                    ->where('firstName', $firstname)
                    ->where('lastName', $lastname)
                    ->where('description', $payable->description)
                    ->where('date', $date)
                    ->where('collectedBy', $collectedBy)
                    ->where('status', $status)
                    ->first();

                if (!$existingPayment) {
                    DB::table('remittance')->insert([
                        'student_id' => $payable->student_id,
                        'firstName' => $firstname,
                        'lastName' => $lastname,
                        'yearLevel' => $payable->yearLevel,
                        'block' => $payable->block,
                        'description' => $payable->description,
                        'paid' => $amountPaid,
                        'role' => $role,
                        'date' => $date,
                        'status' => $status,
                        'date_remitted' => $date,
                        'collectedBy' => $collectedBy,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $newPaidAmount = $existingPayment->paid + $amountPaid;

                    DB::table('remittance')->where('id', $existingPayment->id)->update([
                        'paid' => $newPaidAmount,
                        'updated_at' => now()
                    ]);
                }
            }
        }

        DB::commit();
        \Log::info('Payment saved successfully.');
        return redirect()->back()->with('success', 'Payment saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error while saving payment: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while saving the payment.');
    }
}


public function denomination(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'thousand' => 'nullable|integer|min:0',
        'five_hundred' => 'nullable|integer|min:0',
        'two_hundred' => 'nullable|integer|min:0',
        'one_hundred' => 'nullable|integer|min:0',
        'fifty' => 'nullable|integer|min:0',
        'twenty' => 'nullable|integer|min:0',
        'ten' => 'nullable|integer|min:0',
        'five' => 'nullable|integer|min:0',
        'one' => 'nullable|integer|min:0',
        'twenty_five_cents' => 'nullable|integer|min:0',
        'selectedDates' => 'nullable|string'
    ]);

    $collectedBy = session('firstname') . ' ' . session('lastname');

    $existing = DB::table('denomination')
        ->where('date', $request->date)
        ->where('collectedBy', $collectedBy)
        ->where('status', 'TO TREASURER')
        ->first();

    if ($existing) {
        $updateData = [
            'thousand' => $existing->thousand + ($request->thousand ?? 0),
            'five_hundred' => $existing->five_hundred + ($request->five_hundred ?? 0),
            'two_hundred' => $existing->two_hundred + ($request->two_hundred ?? 0),
            'one_hundred' => $existing->one_hundred + ($request->one_hundred ?? 0),
            'fifty' => $existing->fifty + ($request->fifty ?? 0),
            'twenty' => $existing->twenty + ($request->twenty ?? 0),
            'ten' => $existing->ten + ($request->ten ?? 0),
            'five' => $existing->five + ($request->five ?? 0),
            'one' => $existing->one + ($request->one ?? 0),
            'twenty_five_cents' => $existing->twenty_five_cents + ($request->twenty_five_cents ?? 0),
        ];

        $updateData['totalAmount'] =
            ($updateData['thousand'] * 1000) +
            ($updateData['five_hundred'] * 500) +
            ($updateData['two_hundred'] * 200) +
            ($updateData['one_hundred'] * 100) +
            ($updateData['fifty'] * 50) +
            ($updateData['twenty'] * 20) +
            ($updateData['ten'] * 10) +
            ($updateData['five'] * 5) +
            ($updateData['one'] * 1) +
            ($updateData['twenty_five_cents'] * 0.25);

        DB::table('denomination')
            ->where('date', $request->date)
            ->where('collectedBy', $collectedBy)
            ->update($updateData);
    } else {
        $totalAmount =
            ($request->thousand * 1000) +
            ($request->five_hundred * 500) +
            ($request->two_hundred * 200) +
            ($request->one_hundred * 100) +
            ($request->fifty * 50) +
            ($request->twenty * 20) +
            ($request->ten * 10) +
            ($request->five * 5) +
            ($request->one * 1) +
            ($request->twenty_five_cents * 0.25);

        DB::table('denomination')->insert([
            'date' => $request->date,
            'thousand' => $request->thousand ?? 0,
            'five_hundred' => $request->five_hundred ?? 0,
            'two_hundred' => $request->two_hundred ?? 0,
            'one_hundred' => $request->one_hundred ?? 0,
            'fifty' => $request->fifty ?? 0,
            'twenty' => $request->twenty ?? 0,
            'ten' => $request->ten ?? 0,
            'five' => $request->five ?? 0,
            'one' => $request->one ?? 0,
            'twenty_five_cents' => $request->twenty_five_cents ?? 0,
            'totalAmount' => $totalAmount,
            'collectedBy' => $collectedBy,
            'status' => 'TO TREASURER',
        ]);
    }

    $selectedDates = $request->input('selectedDates');
    if (!empty($selectedDates)) {
        $selectedDates = explode(',', $selectedDates);

        DB::table('remittance')
            ->whereIn('date', $selectedDates)
            ->where('status', 'COLLECTED')
            ->where('collectedBy', $collectedBy)
            ->update([
                'status' => 'TO TREASURER',
                'date_remitted' => $request->input('date') ?? 'N/A'
            ]);
    }

    return redirect()->back()->with('success', 'Denomination saved successfully!');
}

public function getStudents(Request $request)
{
    $date = $request->input('date');
    $collectedBy = $request->input('collectedBy');
    $description = $request->input('description');
    $status = $request->input('status');

    \Log::info("Fetching students for:", [
        'date' => $date,
        'collectedBy' => $collectedBy,
        'description' => $description,
        'status' => $status,
    ]);

    $students = DB::table('remittance')
        ->select(
            'firstname',
            'lastname',
            'yearLevel',
            'block',
            DB::raw('SUM(paid) as paid'),
            'status'
        )
        ->whereDate('date', $date)
        ->where('collectedBy', $collectedBy)
        ->where('description', $description)
        ->where('status', $status)
        ->groupBy('firstname', 'lastname', 'yearLevel', 'block', 'status')
        ->get();

    return response()->json($students);
}

function repPayableManagement() {
    $yearLevels = DB::table('createuser')
        ->select('yearLevel')
        ->distinct()
        ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
        ->get();

    $Payables = DB::table('createpayable')
        ->select(
            'description',
            'dueDate',
            'balance as input_balance',
            DB::raw('COUNT(id) as student_count'),
            DB::raw('(balance * COUNT(id)) as expected_receivable')
        )
        ->groupBy('description', 'dueDate', 'balance')
        ->get();


        $firstname = session('firstname');
        $lastname = session('lastname');



        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();


    return view('representative/RepPayableManagement', compact('Payables', 'yearLevels','profile','firstname','lastname'));
}

public function RepStudentPayables($studentId)
{
    $payables = DB::table('createpayable')
        ->where('student_id', $studentId)
        ->select('student_id', 'description', 'amount', 'id')
        ->get();

    \Log::info('Returning payables:', $payables->toArray());

    return response()->json($payables);
}


public function RepStudentbalance()
{
    $students = DB::table('createuser')
        ->select('student_id', 'lastname', 'firstname', 'yearLevel', 'block', 'role')
        ->whereIn('role', ['student', 'treasurer', 'representative'])
        ->orderBy('lastname', 'asc')
        ->get();

    $payables = DB::table('createpayable')
        ->select('student_id', DB::raw('COALESCE(SUM(amount), 0) as total_balance'))
        ->groupBy('student_id')
        ->get()
        ->keyBy('student_id');

    $yearLevels = DB::table('createuser')
        ->select('yearLevel')
        ->distinct()
        ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
        ->get();

    $blocks = DB::table('createuser')
        ->select('block')
        ->distinct()
        ->orderBy('block')
        ->get();

    $representatives = [];
    foreach ($students as $student) {
        if (strtolower($student->role) === 'representative') {
            $key = strtoupper($student->yearLevel) . ' - ' . strtoupper($student->block);
            $representatives[$key] = $student->firstname . ' ' . $student->lastname;
        }
    }

    $cashOnHand = [];
    $remittancesCash = DB::table('remittance')
        ->select('collectedBy', DB::raw('SUM(paid) as total_paid'))
        ->whereIn('status', ['COLLECTED', 'TO TREASURER'])
        ->groupBy('collectedBy')
        ->get();

    foreach ($remittancesCash as $remit) {
        $cashOnHand[$remit->collectedBy] = $remit->total_paid;
    }


    $remitted = [];
    $remittancesRemitted = DB::table('remittance')
        ->select('collectedBy', DB::raw('SUM(paid) as total_remitted'))
        ->where('status', 'REMITTED')
        ->groupBy('collectedBy')
        ->get();

    foreach ($remittancesRemitted as $remit) {
        $remitted[$remit->collectedBy] = $remit->total_remitted;
    }

    $profile = DB::table('avatar')
    ->where('student_id', session('student_id'))
    ->select('profile')
    ->first();


        $firstname = session('firstname');
        $lastname = session('lastname');

    return view('representative.repStudentBalance', compact(
        'students',
        'payables',
        'yearLevels',
        'blocks',
        'representatives',
        'cashOnHand',
        'remitted',
        'profile',
        'firstname',
        'lastname'
    ));
}

public function RepShowLedger($id)
{
    $student = DB::table('createuser')
        ->where('student_id', $id)
        ->first();

    $payables = DB::table('createpayable')
        ->where('student_id', $id)
        ->select('description', DB::raw('COALESCE(SUM(amount), 0) as total_balance'))
        ->groupBy('description')
        ->get();

    $settledPayables = DB::table('remittance')
        ->where('student_id', $id)
        ->select('date', 'description', 'paid', 'collectedBy', 'status')
        ->orderBy('date', 'asc')
        ->get();


        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();


        $firstname = session('firstname');
        $lastname = session('lastname');

    return view('representative.repStudentLedger', compact('student', 'payables', 'settledPayables', 'profile','firstname','lastname'));
}

public function RepExpense()
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

    return view('representative.RepExpense', compact('firstname', 'lastname', 'paidData', 'groupedExpenses', 'profile', 'sourcesByDate') + [
        'descriptions' => $availableDescriptions->pluck('description'),
    ]);
}


public function getRepExpensesByDateAndSource($date, $source)
{

    $expenses = DB::table('expenses')
        ->whereDate('date', $date)
        ->where('source', $source)
        ->get(['description', 'amount']);

    return response()->json($expenses);
}

public function RepChange(Request $request)
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
