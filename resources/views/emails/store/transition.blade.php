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
    <br>Dersom du vil sjekke en tidligere ordre er disse tilgjengelig på <a href="mystore.123friluft.no">mystore.123friluft.no</a>.
</p>

<p>
    Du får her også tilsendt et nytt passord til vår nye nettbutikk. Du kan logge inn der for å endre opplysninger og passord dersom du ønsker det.
</p>

    <p class="lead text-center">
        Ditt nye passord:<br><br><code style="font-size: 1.2rem;">{{$password}}</code>
    </p>
@stop

@section('footer')
    <p class="lead end">
    <a href="{{url('user/login')}}">Logg inn på 123friluft.no</a>
    </p>
@stop