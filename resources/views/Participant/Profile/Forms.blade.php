@if(session()->has('errorMessage'))
    <input type="hidden" id="errorMessage" value="{{ session('errorMessage') }}">                                    
@endif
@if(session()->has('successMessage'))
    <input type="hidden" id="successMessage" value="{{ session('successMessage') }}">                                    
@endif

@auth
    <form id="sendFriendRequest" class="d-inline" method="POST" action="{{route('participant.friends.update')}}"> 
        @csrf
        <input type="hidden" name="addUserId" value="{{ $userProfile->id }}">                                    
    </form>
    <form id="acceptFriendRequest" class="d-inline" method="POST" action="{{route('participant.friends.update')}}"> 
        @csrf
        <input type="hidden" name="updateUserId" value="{{ $userProfile->id }}">
        <input type="hidden" name="updateStatus" value="accepted">                                                        
    </form>
    <form id="rejectFriendRequest" class="d-inline" method="POST" action="{{route('participant.friends.update')}}"> 
        @csrf
        <input type="hidden" name="updateUserId" value="{{ $userProfile->id }}">                            
        <input type="hidden" name="updateStatus" value="rejected">                                    
    </form>
    <form id="leftFriendRequest" class="d-inline" method="POST" action="{{route('participant.friends.update')}}"> 
        @csrf
        <input type="hidden" name="updateUserId" value="{{ $userProfile->id }}">
        <input type="hidden" name="updateStatus" value="left">                                                                            
    </form>
        <form id="deleteFriendRequest" class="d-inline" method="POST" action="{{route('participant.friends.update')}}"> 
        @csrf
        <input type="hidden" name="deleteUserId" value="{{ $userProfile->id }}">
    </form>
@endauth