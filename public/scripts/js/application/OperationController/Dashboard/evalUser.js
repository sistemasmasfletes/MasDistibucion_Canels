$(document).ready(function()
{
    $('.eval').click(function()
    {
        $('#evaluacion').modal();
        var nombreUsuario = $(this).attr('name');
        $('#nombreUsuario').html(nombreUsuario);
        $('#neutral').attr('checked', 'checked');
    });

    $('#evaluacion .btn-primary').click(function()
    {
        $('#evaluacion').modal('hide');
    });

    $('#evaluacion').on('show.bs.modal', function()
    {
        $('#evaluacion textarea').val('');
    });
});