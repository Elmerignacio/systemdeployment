<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              
    <div class="w-full md:w-72 flex items-center p-2 mt-10 md:mt-20 lg:mt-32">
        <x-trea-components.sorting/>
    </div>

    <div class="w-full md:w-auto flex flex-col items-start space-y-4">
        <div class="text-center md:text-left">     
     {{$slot}}
        </div>
    

    </div>
</div>
