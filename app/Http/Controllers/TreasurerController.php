<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TreasurerController extends Controller
{

    public function dashboard()
    {
        $firstname = session('firstname', 'Guest');
        $lastname = session('lastname', '');
        $role = session('role', 'Guest');
    
        $totalAmount = DB::table('createpayable')->sum('amount');
    
        $totalExpenses = DB::table('expenses')->sum('amount');  
    

        $Payables = DB::table('createpayable')
            ->select(
                'description',
                'dueDate',
                'balance as input_balance',
                DB::raw('COUNT(student_id) as student_count'),
                DB::raw('(balance * COUNT(student_id)) as expected_receivable')
            )
            ->groupBy('description', 'dueDate', 'balance')
            ->get();
    
        $cashOnHand = DB::table('funds')->value('cash_on_hand');
    
        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();
    
        return view('treasurer.dashboard', compact('profile', 'firstname', 'lastname', 'role', 'totalAmount', 'Payables', 'cashOnHand', 'totalExpenses'));
    }

    public function getUserInfo(Request $request)
    {
        return response()->json([
            'firstname' => session('firstname', 'Guest'),
            'lastname' => session('lastname', '')
        ]);
    }
    
    public function expense()
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
    
        return view('Treasurer.expense', compact('firstname', 'lastname', 'paidData', 'groupedExpenses', 'profile', 'sourcesByDate') + [
            'descriptions' => $availableDescriptions->pluck('description'),
        ]);
    }
   public function report()
    {
        $receivables = DB::table('createpayable')
            ->select(
                DB::raw("CONCAT(yearLevel, ' - ', block) as year_and_block"),
                DB::raw('SUM(balance) as total_receivable')
            )
            ->groupBy('yearLevel', 'block')
            ->get();

        $remitted = DB::table('remittance')
            ->where('status', 'Remitted')
            ->select(
                DB::raw("CONCAT(yearLevel, ' - ', block) as year_and_block"),
                DB::raw('SUM(paid) as total_remitted')
            )
            ->groupBy('yearLevel', 'block')
            ->get();

        $groupedData = $receivables->map(function ($receivable) use ($remitted) {
            $match = $remitted->firstWhere('year_and_block', $receivable->year_and_block);
            return (object)[
                'year_and_block' => $receivable->year_and_block,
                'total_receivable' => $receivable->total_receivable,
                'total_remitted' => $match ? $match->total_remitted : 0,
            ];
        });

            $remittanceRecords = DB::table('remittance')
            ->where('status', 'Remitted')
            ->whereIn(DB::raw("CONCAT(yearLevel, ' - ', block)"), $groupedData->pluck('year_and_block')->toArray())
            ->select(
                'student_id',
                'firstName',
                'lastName',
                'yearLevel',
                'block',
                'description',
                'paid',
                'collectedBy as receiver',
                'date_remitted as date'
            )
            ->get();


        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

        $firstname = session('firstname');
            $lastname = session('lastname');

        $treasurer = DB::table('createuser')
        ->where('role', 'Treasurer')
        ->select('firstname', 'lastname')
        ->first();

    $admin = DB::table('createuser')
        ->where('role', 'Admin')
        ->select('firstname', 'lastname')
        ->first();


        return view('Treasurer.Report', compact('firstname', 'lastname', 'groupedData', 'profile', 'remittanceRecords','treasurer','admin'));
    }
        public function fund()
        {
            $receivables = DB::table('createpayable')
                ->select(
                    DB::raw("CONCAT(yearLevel, ' - ', block) as year_and_block"),
                    DB::raw('SUM(balance) as total_receivable')
                )
                ->groupBy('yearLevel', 'block')
                ->get();

            $remitted = DB::table('remittance')
                ->where('status', 'Remitted')
                ->select(
                    DB::raw("CONCAT(yearLevel, ' - ', block) as year_and_block"),
                    DB::raw('SUM(paid) as total_remitted')
                )
                ->groupBy('yearLevel', 'block')
                ->get();

            $groupedData = $receivables->map(function ($receivable) use ($remitted) {
                $match = $remitted->firstWhere('year_and_block', $receivable->year_and_block);
                return (object)[
                    'year_and_block' => $receivable->year_and_block,
                    'total_receivable' => $receivable->total_receivable,
                    'total_remitted' => $match ? $match->total_remitted : 0,
                ];
            });

            $remittanceRecords = DB::table('remittance')
                ->where('status', 'Remitted')
                ->whereIn(DB::raw("CONCAT(yearLevel, ' - ', block)"), $groupedData->pluck('year_and_block')->toArray())
                ->select(
                    'student_id',
                    'firstName',
                    'lastName',
                    'yearLevel',
                    'block',
                    'description',
                    'paid',
                    'collectedBy as receiver',
                    'date_remitted as date'
                )
                ->get();

            $totalExpenses = DB::table('expenses')->sum('amount');


            $cashOnHand = DB::table('available_description')->sum('total_amount_collected');

            $expensesWithDescriptions = DB::table('createpayable')
                ->select('description', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('description')
                ->get();

            $profile = DB::table('avatar')
                ->where('student_id', session('student_id'))
                ->select('profile')
                ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');

            $treasurer = DB::table('createuser')
            ->where('role', 'Treasurer')
            ->select('firstname', 'lastname')
            ->first();

            $admin = DB::table('createuser')
                ->where('role', 'Admin')
                ->select('firstname', 'lastname')
                ->first();


            return view('Treasurer.fund', compact(
                'firstname',
                'lastname',
                'groupedData',
                'profile',
                'remittanceRecords',
                'treasurer',
                'admin',
                'totalExpenses',
                'cashOnHand',
                'expensesWithDescriptions' 
            ));
        }

    public function getExpensesByDateAndSource($date, $source)
    {
        
        $expenses = DB::table('expenses')
            ->whereDate('date', $date)
            ->where('source', $source)
            ->get(['description', 'amount']);
        
        return response()->json($expenses); 
    }
    
    public function storeExpense(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.label' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $description = $request->description;
        $date = $request->date;
        $totalAmount = 0;

        foreach ($request->items as $item) {
            DB::table('expenses')->insert([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'label' => $item['label'],
                'price' => $item['price'],
                'amount' => $item['amount'],
                'date' => $date,
                'source' => $description,
            ]);
            $totalAmount += $item['amount'];
        }

        DB::table('available_description')
            ->where('description', $description)
            ->decrement('total_amount_collected', $totalAmount);

        DB::table('funds')
            ->decrement('cash_on_hand', $totalAmount);

        return redirect()->back()->with('success', 'Expenses recorded and balance updated.');
    }


    
   public function Manageuser()
{
    $students = DB::table('createuser')
        ->where('role', '!=', 'admin')
        ->orderBy('lastname', 'asc')
        ->get();

    $profile = DB::table('avatar')
        ->where('student_id', session('student_id'))
        ->select('profile')
        ->first();

    $firstname = session('firstname');
    $lastname = session('lastname');

    $images = DB::table('avatar')
        ->select('profile')
        ->get();

    $studentsWithProfile = DB::table('createuser')
        ->leftJoin('avatar', 'createuser.student_id', '=', 'avatar.student_id')
        ->where('createuser.role', '!=', 'admin') 
        ->select([
            'createuser.student_id',
            'createuser.firstname',
            'createuser.lastname',
            'createuser.yearLevel',
            'createuser.block',
            'createuser.gender',
            'avatar.profile',
        ])
        ->get()
        ->map(function ($s) {
            $s->profile_url = $s->profile
                ? asset("/storage/{$s->profile}")
                : asset("/storage/images/1.jpg");
            return $s;
        });

    return view('Treasurer/manageUser', compact(
        'studentsWithProfile',
        'images',
        'students',
        'profile',
        'firstname',
        'lastname'
    ));
}


    function Payablemanagement()
    {
       $yearLevels = DB::table('createuser')
        ->select('yearLevel')
        ->whereNotIn('role', ['admin'])
        ->whereNotNull('yearLevel')
        ->where('yearLevel', '!=', '')
        ->distinct()
        ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
        ->get();
        
        $Payables = DB::table('createpayable')
            ->select(
                'description',
                'dueDate',
                'balance as input_balance',
                DB::raw('COUNT(student_id) as student_count'),
                DB::raw('(balance * COUNT(student_id)) as expected_receivable')
            )
            ->groupBy('description', 'dueDate', 'balance')
            ->get();

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');
        return view('Treasurer/payableManagement', compact('Payables', 'yearLevels', 'profile', 'firstname', 'lastname'));
    }

    public function Studentbalance()
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
            ->whereNotIn('role', ['admin'])
            ->whereNotNull('yearLevel')
            ->where('yearLevel', '!=', '')
            ->distinct()
            ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
            ->get();


       $blocks = DB::table('createuser')
            ->select('block')
            ->whereNotIn('role', ['admin'])
            ->whereNotNull('block')
            ->where('block', '!=', '')
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

        return view('Treasurer.studentBalance', compact(
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


    public function Collection()
    {
        $students = DB::table('createuser')
            ->whereIn('role', ['representative', 'student', 'treasurer'])
            ->orderBy('lastname', 'asc')
            ->get();

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

        $firstname = session('firstname');
        $lastname = session('lastname');



        return view('Treasurer/collection', compact('students', 'profile', 'firstname', 'lastname'));
    }

    function Createpayable()
    {
        $yearLevels = DB::table('createuser')
            ->select('yearLevel')
            ->distinct()
            ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
            ->get();

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');

        return view('Treasurer/createPayable', compact('yearLevels', 'profile' , 'firstname', 'lastname'));
    }
    function getStudentsAndBlocks(Request $request)
    {
        $yearLevel = $request->yearLevel;

        $students = DB::table('createuser')
            ->whereIn('role', ['student', 'representative', 'treasurer'])
            ->where('yearLevel', $yearLevel)
            ->get();

        $blocks = DB::table('createuser')
            ->where('yearLevel', $yearLevel)
            ->select('block')
            ->distinct()
            ->orderBy('block', 'asc')
            ->get();


        return response()->json(['students' => $students, 'blocks' => $blocks]);
    }

    function savePayable(Request $req)
    {
        $yearLevel = $req->yearLevel;
        $block = $req->block;
        $student = $req->student_id;

        $query = DB::table('createuser')
            ->whereIn('role', ['student', 'representative', 'treasurer'])
            ->select('student_id', 'yearLevel', 'block', 'firstname', 'lastname', 'role');

        if ($yearLevel !== "all") {
            $query->where('yearLevel', $yearLevel);
        }

        if ($block !== "all") {
            $query->where('block', $block);
        }

        if ($student !== "all") {
            $query->where('student_id', $student);
        }

        $students = $query->get();

        foreach ($students as $stud) {
            $fullName = strtoupper(trim(($stud->firstname ?? '') . ' ' . ($stud->lastname ?? '')));
            $yearLevelUpper = strtoupper($stud->yearLevel);
            $blockUpper = strtoupper($stud->block);
            $descriptionUpper = strtoupper($req->description);
            $amount = $req->amount;
            $dueDate = $req->dueDate;
            $role = strtoupper($stud->role);

            $existingPayable = DB::table('createpayable')
                ->where('student_id', $stud->student_id)
                ->where('description', $descriptionUpper)
                ->where('dueDate', $dueDate)
                ->first();

            if ($existingPayable) {
                DB::table('createpayable')
                    ->where('student_id', $stud->student_id)
                    ->where('description', $descriptionUpper)
                    ->where('dueDate', $dueDate)
                    ->update([
                        'amount' => $existingPayable->amount + $amount
                    ]);
            } else {
                DB::table('createpayable')->insert([
                    'description' => $descriptionUpper,
                    'amount' => $amount,
                    'balance' => $amount,
                    'dueDate' => $dueDate,
                    'yearLevel' => $yearLevelUpper,
                    'block' => $blockUpper,
                    'student_id' => $stud->student_id,
                    'studentName' => $fullName,
                    'role' => $role,

                ]);
            }
        }



        return redirect()->back()->with('success', 'PAYABLES SUCCESSFULLY ADDED FOR SELECTED STUDENTS.');
    }

    public function ArchiveUser()
    {
        $students = DB::table('createuser')->get();
        $archivedStudents = DB::table('archive')->get();

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');

        return view('Treasurer/archiveUser', compact('students', 'archivedStudents', 'profile','firstname','lastname'));
    }
    public function archiveUsers(Request $request)
    {
        $selectedStudents = $request->input('students');

        if (!$selectedStudents) {
            return redirect()->back()->with('error', 'No students selected.');
        }

        $students = DB::table('createuser')->whereIn('student_id', $selectedStudents)->get();

        foreach ($students as $student) {
            DB::table('archive')->insert([
                'student_id' => $student->student_id,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'gender' => $student->gender,
                'yearLevel' => $student->yearLevel,
                'role' => $student->role,
                'block' => $student->block,
                'username' => $student->username,
                'password' => $student->password,
                'status' => 'DEACTIVATED',
            ]);
        }

        DB::table('createuser')->whereIn('student_id', $selectedStudents)->delete();

        return redirect()->back()->with('success', 'Selected students archived successfully.');
    }

    public function getStudentPayables($studentId)
    {
        $payables = DB::table('createpayable')
            ->where('student_id', $studentId)
            ->select('id', 'student_id', 'description', 'amount') // include 'id'
            ->get();

        return response()->json($payables);
    }

   public function SavePayment(Request $req)
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
        // Step 1: Group amounts per payable_id
        $groupedPayments = [];

        foreach ($payableIds as $index => $payableId) {
            if (empty($payableId) || $payableId === 'undefined') continue;

            $amountPaid = floatval($amountsPaid[$index] ?? 0);

            if ($amountPaid <= 0) continue;

            if (!isset($groupedPayments[$payableId])) {
                $groupedPayments[$payableId] = 0;
            }

            $groupedPayments[$payableId] += $amountPaid;
        }

        // Step 2: Process each unique payable
        foreach ($groupedPayments as $payableId => $totalPaid) {
            $payable = DB::table('createpayable')->where('id', $payableId)->first();

            if (!$payable) {
                \Log::warning("Payable ID $payableId not found.");
                continue;
            }

            if ($totalPaid > $payable->amount) {
                return back()->with('error', "Amount paid (₱$totalPaid) exceeds payable amount (₱$payable->amount).");
            }

            $newBalance = $payable->amount - $totalPaid;

            // ✅ Update payable balance only once
            DB::table('createpayable')->where('id', $payableId)->update([
                'amount' => $newBalance
            ]);

            // Get student name split
            [$firstname, $lastname] = explode(' ', trim($payable->studentName), 2) + ['N/A', 'N/A'];

            $status = ($role == 'REPRESENTATIVE') ? 'COLLECTED' : 'COLLECTED BY TREASURER';

            // ✅ Check existing remittance
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
                    'paid' => $totalPaid,
                    'role' => $role,
                    'date' => $date,
                    'status' => $status,
                    'date_remitted' => $date,
                    'collectedBy' => $collectedBy,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $newPaid = $existingPayment->paid + $totalPaid;

                DB::table('remittance')->where('id', $existingPayment->id)->update([
                    'paid' => $newPaid,
                    'updated_at' => now()
                ]);
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



    public function Remitted()
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
            ->orderBy('remittance.date_remitted', 'asc')
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
            ->select('paid', 'description', 'yearLevel', 'block', 'date', 'collectedBy', 'status')
            ->get();

        $collectors = DB::table('createuser')
            ->whereIn('role', ['TREASURER', 'REPRESENTATIVE'])
            ->select('firstname', 'lastname', 'role', 'yearLevel', 'block')
            ->get();

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');

        return view('treasurer.remitted', compact('remittances', 'collectors', 'balances', 'paids', 'profile','firstname','lastname'));
    }

    public function CashOnHand()
    {
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
                'remittance.collectedBy'
            )
            ->whereIn('remittance.status', ['TO TREASURER', 'COLLECTED BY TREASURER'])
            ->orderBy('remittance.status', 'asc')
            ->get();

        $balances = DB::table('createpayable')
            ->select('balance', 'description', 'yearLevel', 'block')
            ->get();

        $paids = DB::table('remittance')
            ->select('paid', 'description', 'yearLevel', 'block', 'date_remitted', 'status')
            ->get();

        $denominations = DB::table('denomination')
            ->select('date', 'thousand', 'five_hundred', 'two_hundred', 'one_hundred', 'fifty', 'twenty', 'ten', 'five', 'one', 'twenty_five_cents', 'totalAmount', 'collectedBy')
            ->get();

        $latestDenomination = DB::table('denomination')
            ->orderByDesc('date')
            ->first();

        $collectors = DB::table('createuser')
            ->select('firstname', 'lastname', 'role', 'yearLevel', 'block')
            ->whereIn('role', ['TREASURER', 'REPRESENTATIVE'])
            ->get();

        foreach ($remittances as $remittance) {
            $remittance->formattedDate = \Carbon\Carbon::parse($remittance->date)->format('Y-m-d');
        }

        $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            $firstname = session('firstname');
            $lastname = session('lastname');

        return view('treasurer.CashOnHand', compact(
            'remittances',
            'balances',
            'denominations',
            'latestDenomination',
            'paids',
            'collectors',
            'profile',
            'firstname',
            'lastname'
        ));
    }


    public function getDenomination(Request $request)
    {
        $date = $request->query('date');
        $collectedBy = $request->query('collectedBy');

        $denomination = DB::table('denomination')
            ->whereDate('date', $date)
            ->where('collectedBy', $collectedBy)
            ->where('status', 'TO TREASURER')
            ->first();

        if (!$denomination) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'date' => $denomination->date,
            'denomination' => $denomination
        ]);
    }

    public function updateRemittanceStatus(Request $request)
    {
        $validated = $request->validate([
            'date_remitted' => 'required|date_format:Y-m-d',
            'collected_by' => 'required|string',
        ]);

        $date_remitted = $validated['date_remitted'];
        $collectedBy = $validated['collected_by'];

        $remittanceIds = DB::table('remittance')
            ->whereDate('date_remitted', $date_remitted)
            ->where('collectedBy', $collectedBy)
            ->where('status', '!=', 'Waiting for Admin Confirmation')
            ->pluck('student_id');


        if ($remittanceIds->isEmpty()) {
            return redirect()->back()->with('info', 'No new remittances to update.');
        }

        $updatedRemittance = DB::table('remittance')
            ->whereIn('student_id', $remittanceIds)
            ->update([
                'status' => 'Waiting for Admin Confirmation',
                'updated_at' => now(),
            ]);

        $updatedDenomination = DB::table('denomination')
            ->whereDate('date', $date_remitted)
            ->where('collectedBy', $collectedBy)
            ->update(['status' => 'Waiting for Admin Confirmation']);

        if ($updatedRemittance && $updatedDenomination) {
            $totalAmount = DB::table('remittance')
                ->whereIn('student_id', $remittanceIds)
                ->sum('paid');

            $existingFund = DB::table('funds')->first();

            if ($existingFund) {
                DB::table('funds')->update([
                    'cash_on_hand' => $existingFund->cash_on_hand + $totalAmount,
                ]);
            } else {
                DB::table('funds')->insert([
                    'cash_on_hand' => $totalAmount,
                    'expenses' => 0,
                    'receivable' => 0,
                ]);
            }
            $descriptions = DB::table('remittance')
                ->select('description', DB::raw('SUM(paid) as total_paid'))
                ->whereIn('student_id', $remittanceIds)
                ->groupBy('description')
                ->get();

            foreach ($descriptions as $desc) {
                $existing = DB::table('available_description')
                    ->where('description', $desc->description)
                    ->first();

                if ($existing) {
                    $newTotalAmount = $existing->total_amount_collected + $desc->total_paid;

                    if ($newTotalAmount > $existing->total_amount_collected) {
                        DB::table('available_description')
                            ->where('description', $desc->description)
                            ->update([
                                'total_amount_collected' => $newTotalAmount,
                            ]);
                    }
                } else {
                    DB::table('available_description')->insert([
                        'description' => $desc->description,
                        'total_amount_collected' => $desc->total_paid,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Status updated, funds added, and available description updated!');
        } else {
            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }


   public function storedenomination(Request $request)
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
    ]);

    $new = [
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
    ];

    $newTotalAmount =
        ($new['thousand'] * 1000) +
        ($new['five_hundred'] * 500) +
        ($new['two_hundred'] * 200) +
        ($new['one_hundred'] * 100) +
        ($new['fifty'] * 50) +
        ($new['twenty'] * 20) +
        ($new['ten'] * 10) +
        ($new['five'] * 5) +
        ($new['one'] * 1) +
        ($new['twenty_five_cents'] * 0.25);

    $collectedBy = session('firstname') . ' ' . session('lastname');

    // Check if a pending entry exists
    $existingDenomination = DB::table('denomination')
        ->where('collectedBy', $collectedBy)
        ->where('status', 'Waiting for Admin Confirmation')
        ->first();

    if ($existingDenomination) {
        DB::table('denomination')
            ->where('id', $existingDenomination->id)
            ->update([
                'totalAmount' => $existingDenomination->totalAmount + $newTotalAmount,
            ]);
    } else {
        DB::table('denomination')->insert([
            'date' => $request->date,
            'thousand' => $new['thousand'],
            'five_hundred' => $new['five_hundred'],
            'two_hundred' => $new['two_hundred'],
            'one_hundred' => $new['one_hundred'],
            'fifty' => $new['fifty'],
            'twenty' => $new['twenty'],
            'ten' => $new['ten'],
            'five' => $new['five'],
            'one' => $new['one'],
            'twenty_five_cents' => $new['twenty_five_cents'],
            'totalAmount' => $newTotalAmount,
            'collectedBy' => $collectedBy,
            'status' => 'Waiting for Admin Confirmation',
        ]);
    }

    // Update remittance statuses
    DB::table('remittance')
        ->where('date_remitted', $request->selectedDateForRequest)
        ->where('status', 'COLLECTED BY TREASURER')
        ->update([
            'status' => 'Waiting for Admin Confirmation',
            'updated_at' => now(),
        ]);

    // Get the descriptions for recently updated remittances
    $descriptions = DB::table('remittance')
        ->select('description', DB::raw('SUM(paid) as total_paid'))
        ->where('status', 'Waiting for Admin Confirmation')
        ->whereDate('updated_at', now()->toDateString())
        ->whereTime('updated_at', '>=', now()->subSeconds(10)->toTimeString())
        ->groupBy('description')
        ->get();

    // Update available_description based on those remittances
    foreach ($descriptions as $desc) {
        $existing = DB::table('available_description')
            ->where('description', $desc->description)
            ->first();

        if ($existing) {
            DB::table('available_description')
                ->where('description', $desc->description)
                ->update([
                    'total_amount_collected' => $existing->total_amount_collected + $desc->total_paid,
                ]);
        } else {
            DB::table('available_description')->insert([
                'description' => $desc->description,
                'total_amount_collected' => $desc->total_paid,
            ]);
        }
    }

    // 👉 Skip fund update since status is still "Waiting for Admin Confirmation"

    return redirect()->back()->with('success', 'Denomination saved successfully and awaiting admin confirmation.');
}



    public function getStudentsWhoPaid(Request $request)
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

    public function userDetails()
    {
        $role = session('role', 'Guest');
        $student_id = session('student_id', '');
        $firstname = session('firstname', '');
        $lastname = session('lastname', '');
        $yearLevel = session('yearLevel', '');
        $block = session('block', '');
        $gender = session('gender', '');
        $username = session('username', '');
        $password = session('password', '');

        $profile = DB::table('avatar')
            ->where('student_id', $student_id)
            ->select('profile')
            ->first();
        //    dd()

        return view('Treasurer.userDetails', compact('profile', 'student_id', 'firstname', 'lastname', 'role', 'yearLevel', 'block', 'username', 'password', 'gender'));
    }


    public function showLedger($id)
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

        return view('Treasurer.studentLedger', compact('student', 'payables', 'settledPayables', 'profile','firstname','lastname'));
    }

    function saveUser(Request $req) {
        $firstNameUpper = strtoupper(trim($req->firstname));
        $lastNameUpper = strtoupper(trim($req->lastname));
        $genderUpper = strtoupper(trim($req->gender));
        $yearLevelUpper = strtoupper(trim($req->yearLevel));
        $roleUpper = strtoupper(trim($req->role));
        $blockUpper = strtoupper(trim($req->block));
    
        DB::table('createuser')->insert([
            'student_id' => $req->student_id,
            'firstname' => $firstNameUpper,
            'lastname' => $lastNameUpper,
            'gender' => $genderUpper,
            'yearLevel' => $yearLevelUpper,
            'role' => $roleUpper,
            'block' => $blockUpper,
            'username' => $req->username,
            'password' => Hash::make($req->password) 
        ]);
    
        return redirect()->back()->with('success', 'Successfully created a user');
    }
    
    public function saveUserImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student_id' => 'required'
        ]);

        $image = $request->file('image');
        $filename = $request->student_id . '_' . time() . '.' . $image->getClientOriginalExtension();

        // Save image to public/user_images/
        $destinationPath = public_path('user_images');
        $image->move($destinationPath, $filename);

        // Update the createuser table (not users)
        DB::table('createuser')->where('student_id', $request->student_id)->update(['profile_image' => $filename]);

        return redirect()->back()->with('success', 'Profile image uploaded successfully.');
    }

    public function change(Request $request)
    {

        $userId = session('student_id', '');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'You need to be logged in.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:3|confirmed',
        ]);


        $user = DB::table('createuser')
            ->where('student_id', $userId)
            ->first();


        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }


        DB::table('createuser')
            ->where('student_id', $user->student_id)
            ->update([
                'password' => Hash::make($request->new_password),
            ]);


        return back()->with('success', 'Password changed successfully.');
    }

    public function modifyUser(Request $request)
    {
        $action = $request->input('action');
        $student_id = $request->input('students.0');
    
        if ($action === 'modify') {
            DB::table('createuser')
                ->where('student_id', $student_id)
                ->update([
                    'firstname' => strtoupper($request->input('firstname')),
                    'lastname' => strtoupper($request->input('lastname')),
                    'gender' => strtolower($request->input('gender')),
                    'yearLevel' => $request->input('yearLevel'),
                    'block' => $request->input('block'),
                ]);
    
            DB::table('createpayable')
                ->where('student_id', $student_id)
                ->update([
                    'studentName' => strtoupper($request->input('firstname')) . ' ' . strtoupper($request->input('lastname')),
                    'yearLevel' => $request->input('yearLevel'),
                    'block' => $request->input('block'),
                ]);
    
            DB::table('remittance')
                ->where('student_id', $student_id)
                ->update([
                    'firstName' => strtoupper($request->input('firstname')),
                    'lastName' => strtoupper($request->input('lastname')),
                    'yearLevel' => $request->input('yearLevel'),
                    'block' => $request->input('block'),
                ]);
    
            return back()->with('success', 'User and related records modified successfully!');
        }
    
        if ($action === 'archive') {
            $user = DB::table('createuser')->where('student_id', $student_id)->first();
    
            if ($user) {
                DB::table('archive')->insert([
                    'student_id' => $user->student_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'gender' => $user->gender,
                    'yearLevel' => $user->yearLevel,
                    'role' => $user->role,
                    'block' => $user->block,
                    'status' => 'DEACTIVATED',
                    'username' => $user->username,
                    'password' => $user->password,
                ]);
    
                DB::table('createuser')->where('student_id', $student_id)->delete();
    
                DB::table('createpayable')->where('student_id', $student_id)->delete();
    
                DB::table('remittance')
                ->where('student_id', $student_id)
                ->where('status', '!=', 'remitted') 
                ->delete();
            
            return back()->with('success', 'User and related records archived and deleted successfully!');
            
            }
    
            return back()->with('error', 'User not found.');
        }
    
        return back()->with('error', 'Invalid action.');
    }

      // Delete Payable and related data
 public function deletePayable($description)
{
    DB::beginTransaction();

    try {
        DB::table('remittance')->where('description', $description)->delete();
        DB::table('available_description')->where('description', $description)->delete();
        DB::table('expenses')->where('source', $description)->delete();
        DB::table('createpayable')->where('description', $description)->delete();

        DB::commit();

        return redirect()->back()->with('success', 'Payable deleted successfully.');
        // Or redirect to specific route like:
        // return redirect('/payable-management')->with('success', 'Deleted!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
    }
}



  
public function update(Request $request, $encodedDescription)
{
    // Decode the description
    $description = urldecode($encodedDescription);

    // Get the data from the request
    $amount = $request->input('amount');
    $dueDate = $request->input('dueDate');
    
    // Validate input
    if (!$amount || !$dueDate) {
        return redirect()->back()->withErrors('Amount and Due Date are required.');
    }

    // Check if the record exists
    $payable = DB::table('createpayable')->where('description', $description)->first();

    if (!$payable) {
        return redirect()->back()->withErrors('No payable found with the provided description.');
    }

    // Perform the update using Laravel's DB query builder
    DB::table('createpayable') // Your table name
        ->where('description', $description) // Match the description
        ->update([
            'amount' => $amount,
            'dueDate' => $dueDate,
            'updated_at' => now(), // Ensure updated_at field is set
        ]);

    // Redirect back with success message
    return redirect()->back()
        ->with('success', 'Payable updated successfully!');
}
    


}

