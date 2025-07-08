<!-- PAYMENT FORM WRAPPER -->
<div 
x-show="showDetails"
x-transition:enter="transition duration-300 transform"
x-transition:enter-start="-translate-y-10 opacity-0"
x-transition:enter-end="translate-y-0 opacity-100"
x-transition:leave="transition duration-200 transform"
x-transition:leave-start="translate-y-0 opacity-100"
x-transition:leave-end="-translate-y-10 opacity-0"
class="h-[100%] w-full md:w-1/2 mx-auto p-3 mt-4 bg-gray-300 bg-opacity-40 shadow-lg border-2 border-green-700 rounded-lg relative"
>

<div x-show="showDetails" x-transition>
<form id="paymentForm" action="{{ route('treasave.payment') }}" method="POST">
@csrf
<input type="hidden" name="student_id" id="studentId" x-model="studentId">

<div>
  <p id="studentName" class="text-[25px] font-bold text-green-700" x-text="studentName">SELECT A STUDENT</p>
  <p id="studentYearBlock" class="text-[19px] font-bold text-gray-600" x-text="studentYearBlock"></p>
</div>

<div class="flex items-center space-x-2 mt-4">
  <input type="date" id="schoolYearFilter" name="date" class="border border-black rounded-md px-3 py-1 text-sm focus:ring-2 focus:ring-green-500" required>
</div>

<div class="mt-2">
  <x-scrollable-table height="max-h-[35vh]">
    <thead>
      <tr class="bg-green-700 text-white text-center">
        <th class="p-2 border border-black">DESCRIPTION</th>
        <th class="p-2 border border-black">REMAINING BALANCE</th>
        <th class="p-2 border border-black">AMOUNT PAID</th>
      </tr>
    </thead>
    <tbody class="bg-white text-center" id="payablesTableBody"></tbody>
    <tfoot>
      <tr class="text-white font-bold bg-green-700 text-center">
        <td class="p-2 border border-black">TOTAL</td>
        <td class="p-2 border border-black" id="totalRemaining">₱0.00</td>
        <td class="p-2 border border-black" id="totalAmountPaid">₱0.00</td>
      </tr>
    </tfoot>
  </x-scrollable-table>
</div>

<div class="mt-4">
  <button type="button" id="receiveBtn" class="px-6 py-3 bg-green-700 text-white rounded-lg font-bold">
    RECEIVE
  </button>
  <x-trea-components.receive-payment-modal/>
</div>
</form>
</div>
</div>

<!-- Alpine Component Setup -->
<script>
function collectionsApp() {
  return {
    showDetails: false,
    studentName: '',
    studentYearBlock: '',
    studentId: '',
    handleClick(studentId, name, yearBlock) {
      this.studentName = name;
      this.studentYearBlock = yearBlock;
      this.studentId = studentId;
      this.showDetails = true;
      handleRowClick(studentId, name, yearBlock);
    }
  }
}
</script>

<!-- Modal Button Logic -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const archiveModal = document.getElementById("archiveModalMale");
  const successModal = document.getElementById("successModalMale");
  const confirmButton = archiveModal?.querySelector(".confirmBtn");
  const cancelButton = archiveModal?.querySelector(".cancelBtn");
  const successConfirmButton = successModal?.querySelector("button[type='submit']");

  confirmButton?.addEventListener("click", function () {
    successModal?.classList.add("hidden");
  });

  confirmButton?.addEventListener("click", function () {
    archiveModal?.classList.add("hidden");
    successModal?.classList.remove("hidden");
  });

  cancelButton?.addEventListener("click", function () {
    archiveModal?.classList.add("hidden");
  });

  successConfirmButton?.addEventListener("click", function () {
    successModal?.classList.add("hidden");
  });
});
</script>

<!-- Payment Interaction Logic -->
<script>
function handleRowClick(studentId, fullName, yearBlock) {
  const nameElem = document.getElementById("studentName");
  const blockElem = document.getElementById("studentYearBlock");
  const hiddenIdElem = document.getElementById("studentId");
  const modalElem = document.getElementById("paymentModal");

  if (nameElem) nameElem.textContent = fullName;
  if (blockElem) blockElem.textContent = yearBlock;
  if (hiddenIdElem) hiddenIdElem.value = studentId;

  fetchStudentPayables(studentId);

  if (modalElem && modalElem.classList) {
    modalElem.classList.remove("hidden");
  }
}

function fetchStudentPayables(studentId) {
  fetch(`/treasurer/get-student-payables/${studentId}`)
    .then(response => response.json())
    .then(data => renderPayables(data))
    .catch(error => console.error("Error fetching student payables:", error));
}

function renderPayables(data) {
  const tbody = document.getElementById("payablesTableBody");
  tbody.innerHTML = "";

  const remainingPayables = data.filter(payable => parseFloat(payable.amount) > 0);

  if (remainingPayables.length > 0) {
    remainingPayables.forEach(payable => {
      const formattedAmount = parseFloat(payable.amount).toFixed(2);
      const row = `
        <tr>
          <td class="p-2 border border-black">${payable.description}</td>
          <td class="p-2 border border-black remaining-balance">₱${formattedAmount}</td>
          <td class="p-2 border border-black">
            <input type="number" name="amount_paid[]" class="rounded-md p-1 w-20 amount-paid" min="0" step="0.01"
              oninput="updateTotals(); checkPaymentInputs();">
           <input type="hidden" name="payable_id[]" value="${payable.id ?? ''}">


            <input type="hidden" class="original-balance" value="${formattedAmount}">
          </td>
        </tr>`;
      tbody.innerHTML += row;
    });
  } else {
    tbody.innerHTML = `<tr><td colspan="3" class="p-2 border border-black text-red-500">No remaining balances</td></tr>`;
  }

  updateTotals();
  checkPaymentInputs();
}

function updateTotals() {
  let totalRemaining = 0;
  let totalPaid = 0;

  document.querySelectorAll(".amount-paid").forEach((input, index) => {
    const originalBalance = parseFloat(document.querySelectorAll(".original-balance")[index].value);
    let paidAmount = parseFloat(input.value) || 0;
    let newBalance = originalBalance - paidAmount;

    if (newBalance < 0) {
      input.value = originalBalance;
      newBalance = 0;
    }

    document.querySelectorAll(".remaining-balance")[index].textContent = `₱${newBalance.toFixed(2)}`;
    totalRemaining += newBalance;
    totalPaid += paidAmount;
  });

  document.getElementById("totalRemaining").textContent = `₱${totalRemaining.toFixed(2)}`;
  document.getElementById("totalAmountPaid").textContent = `₱${totalPaid.toFixed(2)}`;
}

function closeModal() {
  document.getElementById("paymentModal")?.classList.add("hidden");
}

document.getElementById("submitPayment")?.addEventListener("click", function (event) {
  event.preventDefault();

  const dateInput = document.getElementById("schoolYearFilter").value;
  if (!dateInput) {
    alert("Please select a date before submitting!");
    return;
  }

  const formElem = document.getElementById("paymentForm");
  if (!formElem) {
    alert("Payment form not found.");
    return;
  }

  const formData = new FormData(formElem);
  formData.append("date", dateInput);

  fetch("/treasurer/save-payment", {
    method: "POST",
    body: formData,
    headers: {
      "Accept": "application/json" 
    }
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Payment saved successfully!");
        const studentId = document.getElementById("studentId").value;
        fetchStudentPayables(studentId);
        closeModal();
      } else {
        alert("Error: " + (data.error || "Something went wrong."));
      }
    })
    .catch(error => {
      console.error("Error saving payment:", error);
      alert("A network or server error occurred.");
    });
});

function checkPaymentInputs() {
  let hasPayment = false;
  document.querySelectorAll(".amount-paid").forEach(input => {
    if (parseFloat(input.value) > 0) {
      hasPayment = true;
    }
  });

  const submitBtn = document.getElementById("submitPayment");
  if (submitBtn) {
    submitBtn.disabled = !hasPayment;
  }
}

document.getElementById("paymentModal")?.addEventListener("input", function () {
  checkPaymentInputs();
});
</script>

