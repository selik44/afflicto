@extends('emails.master')

@section('content')
<p class="lead text-center">
     Hei! Vi legger idag om til ny nettbutikk.
</p>
<p>
    I den anledning vil det bli en del forandringer både utseendemessig og i systemet bak.
</p>
<p>
    Dersom du tidligere har bestilt varer hos oss, vi ikke disse ordrene følge med over i det nye systemet.
    <br>Du kan sjekke en tidligere ordre er disse tilgjengelig på <a href="mystore.123friluft.no">mystore.123friluft.no</a>.
</p>

<p>
    Vi har opprettet en ny konto for deg, med email addresse: someone@somewhere.com og passord <code style="font-size: 1.2rem;">14fk692Mfa</code>.
</p>
@stop

@section('footer')
    <a href="{{url('user/login')}}">@lang('emails.welcome.login')</a>
@stop