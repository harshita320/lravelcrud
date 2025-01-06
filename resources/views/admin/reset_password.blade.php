<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body class="container">
    <h1>reset password </h1>



@if($errors->any())
@foreach ( $errors->all() as $error )
    <li>{{ $error }}</li>
@endforeach
@endif


@if(Session::has('error'))
<li>{{ Session::get('error') }}</li>
@endif


@if(Session::has('success'))
<li>{{ Session::get('success') }}</li>
@endif


    <form action="{{ route('admin.reset_password_submit') }}"   method="post">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">new password </label>
          <input type="password" name="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
          
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">confirmed newPassword</label>
          <input type="password"  name="password_confirmation" class="form-control" id="exampleInputPassword1">
        </div>
        <div class="mb-3 form-check">
         
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
</body>
</html>