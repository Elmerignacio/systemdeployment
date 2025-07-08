@props(['height' => 'max-h-[50vh]']) 

    <div class="w-full md:w-1/2 overflow-auto">
        <div class="overflow-x-auto {{ $height }} custom-scrollbar border rounded-md relative mt-4 sm:mr-4 md:mr-6 lg:mr-8 xl:mr-10">
            <table class="w-full border-collapse">
                {{ $slot }}
            </table>
        </div>
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

