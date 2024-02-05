@extends('adminlte::page')

@section('title', 'MGSI')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="font-weight-bold text-center mx-auto">Responder Proceso H</h1>
    <a href="{{ route('procesoH.index', ['return_url' => url()->current()]) }}" class="btn" style="background-color: #AAADAD; color: #FFFFFF; padding-left: 10px;" tabindex="6">
        <i class="fas fa-chevron-left" style="margin-right: 5px;"></i>
        Volver
    </a>
</div>
<br>
{{ Breadcrumbs::render('responderprocesoH') }}

@stop

@section('content')

<div class="card">
<div class="card-body">

@if (session('success1'))<!--Alerta de Actualización exitosa del Archivo-->
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success1') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Otra alerta de éxito -->
    @if (session('success2'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success2') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="attachment-section">
    <h4>Archivos adjuntos de la documentación del semestre: {{ $documentacion->semestre }} </h4>

    <div class="card card-warning collapsed-card mb-4">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body ">
            @if (!empty($archivosAdjuntos))
            
                @foreach ($archivosAdjuntos as $archivoPath)
                    <li>
                        <a href="{{ asset('storage/' . $archivoPath) }}" target="_blank">{{ basename($archivoPath) }}</a>
                    </li>
                @endforeach
            @else
                <p>No se han subido archivos adjuntos para este semestre.</p>
            @endif
        </div>
    </div>
</div>

@if ($respuesta) <!--Se mostrará la mostrar información sobre la respuesta actual si existe una respuesta-->

<h3>Respuesta actual:</h3>
<table class="table table-bordered table-responsive-sm table-bordered mt-4">
<thead style="background-color: #2e7555!important;" class="text-white">
    <tr>
        <th>Archivo</th>
        <th>Acciones</th>
        <th>Calificacion</th>
    </tr>
    </thead>
    <tr>
        <td>
             @foreach(explode('|', $respuesta->archivo_path) as $archivoPath)
            <a href="{{ asset('storage/' . $archivoPath) }}" target="_blank"> {{ basename($archivoPath) }}</a>
            <br>
            @endforeach
        </td> 
        <td>
            @if (count(explode('|', $respuesta->archivo_path)) > 1)
            <a href="{{ route('procesoH.descargarArchivos', ['id' => $respuesta->id]) }}">Descargar archivos (ZIP)</a><br>
            @else
                @foreach(explode('|', $respuesta->archivo_path) as $archivoPath)
                    <a href="{{ asset('storage/' . $archivoPath) }}" download>Descargar archivo</a><br>
                @endforeach
            @endif
            <a href="#" data-toggle="modal" data-target="#editarRespuestaModal">Editar respuesta</a>
        </td>
        <td>
            @if ($respuesta->calificacion) <!-- Verificar si existe una calificación -->
            <a href="#" data-toggle="modal" data-target="#verCalificacionModal{{ $respuesta->calificacion->id }}">Ver calificación</a> <!--El ID de la calificación se pasa a la ruta para mostrar los detalles de la calificación -->
            @else
            Sin calificar
            @endif
        </td>
    </tr>
</table>


<!-- Ventana modal para editar respuesta -->
<div class="modal fade" id="editarRespuestaModal" tabindex="-1" role="dialog" aria-labelledby="editarRespuestaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="editarRespuestaModalLabel">Editar respuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Envío de parámetros para identificar la respuesta y documentación asociada-->
            {!! Form::open(['route' => ['procesoH.actualizarRespuesta', $respuesta->id, $documentacion->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
            <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('archivos', 'Nuevos archivos:') !!}
                        <div class="custom-file">
                            {{ Form::file('archivo[]', ['id' => 'archivos', 'class' => 'custom-file-input', 'multiple', 'accept' => '.pdf,.doc,.docx,.xlsx']) }}
                            {!! Form::label('archivos', 'Seleccionar archivos', ['class' => 'custom-file-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    {!! Form::submit('Guardar cambios', ['class' => 'btn btn-primary']) !!}
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@else
<!--Bloque alternativo en caso de no existir alguna respuesta, a su vez dando la opción de dar respuesta a la misma-->
<h3>Responder:</h3>
{!! Form::open(['route' => ['procesoH.guardarRespuesta', 'documentacionId' => $documentacion->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
    @csrf
    <div class="form-group">
    {{ Form::label('archivos', 'Archivos:') }}
    <div class="custom-file">
        {{ Form::file('archivo[]', ['id' => 'archivos', 'class' => 'custom-file-input', 'multiple', 'accept' => '.pdf,.doc,.docx,.xlsx']) }}
        {{ Form::label('archivos', 'Seleccionar archivos', ['class' => 'custom-file-label']) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label('archivosSeleccionados', 'Archivos seleccionados:') }}
    <br>
    <label id="archivosSeleccionados" class="mt-2"></label> <!-- Para mostrar los nombres de los archivos seleccionados -->
</div>
{{ Form::submit('Enviar respuesta', ['class' => 'btn btn-primary']) }}
{!! Form::close() !!}
@endif

<!-- Ventana modal para ver la calificación -->
<!--Se verifica si la variable respuesta existe y tiene un valor distinto a uno nulo y se verifica
 si la propiedad calificación de la variable respuesta existe y es diferente a nulo-->
@if ($respuesta && $respuesta->calificacion)
<!--Concatenación con el id de la calificación asociada-->
<div class="modal fade" id="verCalificacionModal{{ $respuesta->calificacion->id }}" tabindex="-1" role="dialog" aria-labelledby="verCalificacionModalLabel{{ $respuesta->calificacion->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="verCalificacionModalLabel{{ $respuesta->calificacion->id }}">Detalles de la calificación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <p>Cumplimiento:
                            @if ($respuesta->calificacion->cumplimiento == 'Si') <!--Obtención del cumplimiento de la calificación asociada a la respuesta-->
                            <i class="fas fa-check text-success"></i> <!-- Icono de paloma -->
                            @else
                            <i class="fas fa-times text-danger"></i> <!-- Icono de tache -->
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p>Porcentaje de cumplimiento: {{ $respuesta->calificacion->porcentajeCumplimiento }}</p> <!--Obtención del porcentaje de cumplimiento de la calificación asociada a la respuesta-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <p>Estado de la documentación:
                            @if ($respuesta->calificacion->estadoDocumentacion == 'Pendiente') <!--Obtención del estado de la documentación de la calificación asociada a la respuesta-->
                            <i class="fas fa-clock text-warning"></i> <!-- Icono de pendiente -->
                            @elseif ($respuesta->calificacion->estadoDocumentacion == 'Concluido')
                            <i class="fas fa-check-circle text-success"></i> <!-- Icono de concluido -->
                            @else
                            <i class="fas fa-exclamation-circle text-danger"></i> <!-- Icono de faltante -->
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p>Comentario: {{ $respuesta->calificacion->comentario }}</p> <!--Obtención del comentario de la calificación asociada a la respuesta-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center ">
                        <canvas id="donutChart{{ $respuesta->calificacion->id }}" style="max-width: 900px; max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif
</div>
</div>  

@stop

@section('css')
<link rel="stylesheet" href="{{  asset('assests/css/jquery-ui.css') }}">
@stop

@section('js')

<script src="{{ asset('assests/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assests/js/jquery.ui.datepicker-es.js') }}"></script>
<script src="{{ asset('assests/js/chart.js') }}"></script>
<script src="{{ asset('assests/js/procesos/motrar-nombre-subida-archivos.js') }}"></script>

<script>
    $(document).ready(function() {
        @if ($respuesta && $respuesta->calificacion)
            var donutData{{ $respuesta->calificacion->id }} = {
                labels: ['Cumplimiento', 'Restante'],
                datasets: [{
                    data: [
                        {{ $respuesta->calificacion->porcentajeCumplimiento }},
                        {{ 100 - $respuesta->calificacion->porcentajeCumplimiento }}
                    ],
                    backgroundColor: [
                        '{{ $respuesta->calificacion->porcentajeCumplimiento >= 50 ? 'rgba(0, 255, 35, 100)' : 'red' }}',
                        'lightgray'
                    ]
                }]
            };

            var donutOptions{{ $respuesta->calificacion->id }} = {
                responsive: true,
                cutoutPercentage: 40,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var currentValue = dataset.data[tooltipItem.index];
                            return currentValue + '%';
                        }
                    }
                }
            };

            var donutChart{{ $respuesta->calificacion->id }} = new Chart(document.getElementById('donutChart{{ $respuesta->calificacion->id }}'), {
                type: 'doughnut',
                data: donutData{{ $respuesta->calificacion->id }},
                options: donutOptions{{ $respuesta->calificacion->id }}
            });
        @endif
    });
</script>

@stop