@csrf
@if ($errors->any())
     <div class="text-danger">
         <ul>
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
@if (session()->has('errorMessage'))
    <div class="text-danger">
        Error changing the name. Can you please try a unique name for your team?
    </div>
@endif
