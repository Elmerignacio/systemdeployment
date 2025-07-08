<a href="#" onclick="closeModal()" class="text-lg ml-auto">
    <i class="fas fa-times text-1xl hover:text-red-600"></i>
</a>

<script>
    function openModal() {
        const modal = document.getElementById("createUserModal");
        modal.classList.remove("hidden", "opacity-0", "pointer-events-none");
        modal.children[0].classList.remove("scale-95");
        modal.children[0].classList.add("scale-100");
    }
    
    function closeModal() {
        const modal = document.getElementById("createUserModal");
        modal.classList.add("opacity-0", "pointer-events-none");
        modal.children[0].classList.remove("scale-100");
        modal.children[0].classList.add("scale-95");
    
        setTimeout(() => {
            modal.classList.add("hidden");
        }, 300); 
    }
</script>