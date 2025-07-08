@props(['height' => 'max-h-[50vh]', 'width' => 'w-[500px]'])



<div class="overflow-x-auto {{ $height }} {{ $width }} custom-scrollbar border rounded-md relative">


    <table class="w-full border-collapse">
        {{ $slot }}
    </table>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #15803d;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #15803d;
    }

    thead {
        position: sticky;
        top: 0;
        background-color: #15803d;
        z-index: 10;
    }

    tfoot {
        position: sticky;
        bottom: 0;
        background-color: #15803d;
        z-index: 5;
    }
</style>
