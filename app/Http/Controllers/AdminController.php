<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{

        public function AdDashboard()
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
                    DB::raw('COUNT(id) as student_count'),
                    DB::raw('(balance * COUNT(id)) as expected_receivable')
                )
                ->groupBy('description', 'dueDate', 'balance')
                ->get();

            $cashOnHand = DB::table('funds')->value('cash_on_hand');

            $profile = DB::table('avatar')
            ->where('student_id', session('student_id'))
            ->select('profile')
            ->first();

            return view('admin.AdDashboard', compact('profile', 'firstname', 'lastname', 'role', 'totalAmount', 'Payables', 'cashOnHand', 'totalExpenses'));
        }



        public function getUserInfo(Request $request)
        {
            return response()->json([
                'firstname' => session('firstname', 'Guest'),
                'lastname' => session('lastname', '')
            ]);
        }
        public function AdExpense()
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

        return view('admin.AdExpense', compact('firstname', 'lastname', 'paidData', 'groupedExpenses', 'profile', 'sourcesByDate') + [
            'descriptions' => $availableDescriptions->pluck('description'),
        ]);

        }

    public function getAdExpensesByDateAndSource($date, $source)
    {

        $expenses = DB::table('expenses')
            ->whereDate('date', $date)
            ->where('source', $source)
            ->get(['description', 'amount']);

        return response()->json($expenses);
    }

    public function AddStoreExpense(Request $request)
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

      public function AdManageUser()
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

    return view('Admin/AdManageUser', compact(
        'studentsWithProfile',
        'images',
        'students',
        'profile',
        'firstname',
        'lastname'
    ));
}

        function AdPayableManagement()
        {
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

                $profile = DB::table('avatar')
                ->where('student_id', session('student_id'))
                ->select('profile')
                ->first();

                $firstname = session('firstname');
                $lastname = session('lastname');
            return view('Admin/AdPayableManagement', compact('Payables', 'yearLevels', 'profile', 'firstname', 'lastname'));
        }

        public function AdStudentBalance()
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

            return view('Admin.AdStudentBalance', compact(
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



        function Createpayable()
        {
            $yearLevels = DB::table('createuser')
                ->select('yearLevel')
                ->distinct()
                ->orderByRaw("FIELD(yearLevel, '1st year', '2nd year', '3rd year', '4th year')")
                ->get();

            $profile = DB::table('avatar')
                ->where('student_id', session('id'))
                ->select('profile')
                ->first();

                $firstname = session('firstname');
                $lastname = session('lastname');

            return view('Admin/createPayable', compact('yearLevels', 'profile' , 'firstname', 'lastname'));
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

        public function AdArchiveUser()
        {
            $students = DB::table('createuser')->get();
            $archivedStudents = DB::table('archive')->get();

            $profile = DB::table('avatar')
                ->where('student_id', session('id'))
                ->select('profile')
                ->first();

                $firstname = session('firstname');
                $lastname = session('lastname');

            return view('Admin/AdArchiveUser', compact('students', 'archivedStudents', 'profile','firstname','lastname'));
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
                ->select('student_id', 'description', 'amount', 'id')
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




        public function AdUserDetails()
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
                ->where('student_id', $student_id)
                ->select('profile')
                ->first();
            //    dd()



            return view('Admin.AdUserDetails', compact('profile', 'student_id', 'firstname', 'lastname', 'role', 'yearLevel', 'block', 'username', 'password', 'gender'));
        }


        public function AdStudentLedger($id)
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

            return view('Admin.AdStudentLedger', compact('student', 'payables', 'settledPayables', 'profile','firstname','lastname'));
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
        public function AdReport()
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
                    'id',
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


            return view('Admin.AdReport', compact('firstname', 'lastname', 'groupedData', 'profile', 'remittanceRecords','treasurer','admin'));
        }
        public function AdFund()
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
            'id',
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


    return view('Admin.AdFund', compact(
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



        public function saveUserImage(Request $request)
        {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'student_id' => 'required'
            ]);

            $image = $request->file('image');
            $filename = $request->student_id . '_' . time() . '.' . $image->getClientOriginalExtension();

            $destinationPath = public_path('user_images');
            $image->move($destinationPath, $filename);

            DB::table('createuser')->where('student_id', $request->student_id)->update(['profile_image' => $filename]);

            return redirect()->back()->with('success', 'Profile image uploaded successfully.');
        }



        public function Adchange(Request $request)
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


function AdsaveUser(Request $req) {
    $firstNameUpper = strtoupper(trim($req->firstname));
    $lastNameUpper = strtoupper(trim($req->lastname));
    $genderUpper = strtoupper(trim($req->gender));
    $roleUpper = strtoupper(trim($req->role));

    $yearLevel = '';
    $block = '';

    if ($roleUpper !== 'ADMIN') {
        $yearLevel = strtoupper(trim($req->yearLevel));
        $block = strtoupper(trim($req->block));
    }

    DB::table('createuser')->insert([
        'student_id' => $req->student_id,
        'firstname' => $firstNameUpper,
        'lastname' => $lastNameUpper,
        'gender' => $genderUpper,
        'yearLevel' => $yearLevel,
        'block' => $block,
        'role' => $roleUpper,
        'username' => $req->username,
        'password' => Hash::make($req->password),
    ]);

    return redirect()->back()->with('success', 'Successfully created a user');
}



public function AdRemitted()
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

    return view('admin.AdRemitted', compact('remittances', 'collectors', 'balances', 'paids', 'profile','firstname','lastname'));
}
public function fetchStudents(Request $request)
{
    try {
        $status = $request->input('status');
        $date = $request->input('date');
        $collectedBy = $request->input('collectedBy');
        $description = $request->input('description');

        $students = DB::table('remittance')
            ->select('id', 'firstName as firstname', 'lastName as lastname', 'description', 'paid')
            ->where('status', $status)
            ->whereDate('date', $date)
            ->where('collectedBy', $collectedBy)
            ->where('description', $description)
            ->get();

        return response()->json($students);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function approve(Request $request)
{
    $dates = $request->input('dates'); // array of 'YYYY-MM-DD'

    if (!is_array($dates) || empty($dates)) {
        return response()->json(['message' => 'No dates provided.'], 400);
    }

    DB::beginTransaction();

    try {
        // 1. Get total amount ONLY from records with 'WAITING FOR ADMIN CONFIRMATION'
        $totalRemittance = DB::table('remittance')
            ->whereIn(DB::raw('DATE(date)'), $dates)
            ->where('status', 'WAITING FOR ADMIN CONFIRMATION')
            ->sum('paid');

        // 2. Update only those with status = 'WAITING FOR ADMIN CONFIRMATION'
        DB::table('remittance')
            ->whereIn(DB::raw('DATE(date)'), $dates)
            ->where('status', 'WAITING FOR ADMIN CONFIRMATION')
            ->update([
                'status' => 'REMITTED',
                'date_remitted' => \Carbon\Carbon::now(),
                'updated_at' => now()
            ]);

        // 3. Add the total to cash_on_hand
        $funds = DB::table('funds')->first();

        if ($funds) {
            DB::table('funds')->update([
                'cash_on_hand' => $funds->cash_on_hand + $totalRemittance
            ]);
        } else {
            DB::table('funds')->insert([
                'cash_on_hand' => $totalRemittance,
                'expenses' => 0,
                'receivable' => 0,
            ]);
        }

        DB::commit();

        return response()->json(['message' => 'Remittances approved and funds updated successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Remittance approval failed: ' . $e->getMessage());
        return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
    }
}
    

public function AdCashOnHand()
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

    // Format the date for each remittance
    foreach ($remittances as $remittance) {
        $remittance->formattedDate = \Carbon\Carbon::parse($remittance->date)->format('Y-m-d');
    }

    $profile = DB::table('avatar')
        ->where('student_id', session('student_id'))
        ->select('profile')
        ->first();

        $firstname = session('firstname');
        $lastname = session('lastname');

    return view('admin.AdCashOnHand', compact(
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



}
