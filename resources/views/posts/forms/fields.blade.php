
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{!! Form::open(['route' => 'store']) !!}
<div class="form-group">
        <label for="exampleFormControlInput1">Titulo</label>
        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" name="title">
    </div>
    <div class="form-group">
        <label for="exampleFormControlTextarea1">Cuerpo</label>
        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="body"></textarea>
    </div>
    <div class="form-group">

        <input type="submit" value="Enviar" placeholder="">

    </div>
{!! Form::close() !!}