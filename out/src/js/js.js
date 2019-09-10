// A $( document ).ready() block.
$( document ).ready(function() {

    $('#button_new').click(function() {
        if(confirm('Do you really want to start a new project?'))
        {
            displayWait();
            current_project_name="";
            $('#container svg, #container .table').each(function() {
                $(this).remove();
                $('#tables .navitable input').prop( "checked", false );
            });
            refreshProject();
            hideWait();
        }
    });

    var current_project_name="";
    $('#button_save').click(function() {
        displayWait()
        current_project_name = prompt('Name of the project?',current_project_name);
        if(current_project_name!="")
        {

            if(!current_project_name.toLowerCase().endsWith('.cpfdb'))
                current_project_name += '.cpfdb';

            var params = $('#form_tables_save').serialize();
            params+="&filename=" + current_project_name;

            $('#container .table').each(function() {
                var id=$(this).attr('id');
                var pos = $(this).position();
                var left = pos.left;
                var top = pos.top;
                var width = $(this).width();
                var height = $(this).height();

                params+="&table[]=" + id;
                params+="&left[]=" + left;
                params+="&top[]=" + top;
                params+="&width[]=" + width;
                params+="&height[]=" + height;
            });
            //console.log(params);
            var url=$('#form_tables_save').attr('action');
            $.ajax({
                method: "POST",
                url: url,
                data: params,
                async: false
            })
            .done(function( html ) {
                alert('Project saved');
            });
            refreshProject();
        }
        hideWait();
    });

    var current_project_name="";
    $('#button_load_popup').click(function() {

        displayWait();

        $('#popup_load select').find('option').remove();

        var params = $('#form_tables_load_project_list').serialize();
        var url=$('#form_tables_load_project_list').attr('action');
        $.ajax({
            method: "POST",
            url: url,
            data: params,
            async: false
        })
        .done(function( data ) {
            var result = JSON.parse(data);
            for(var k in result) {
                var projectfile = result[k];
                $('#popup_load select').append('<option value="'+projectfile+'">'+projectfile+'</option>');
            }
        });

        $('#popup_load').css({'display':'block'});
        $('#popup_load').css({
            "z-index": (tableselected_zindex + 100)
        });
    });
    $('#button_load_cancel').click(function() {
        $('#popup_load').css({'display':'none'});
        hideWait();
    });
    $('#button_load').click(function() {
        current_project_name = $('#popup_load select').val();
        $('#popup_load').css({'display':'none'});

        if(current_project_name!="")
        {

            $('#container svg, #container .table').each(function() {
                $(this).remove();
                $('#tables .navitable input').prop( "checked", false );
            });

            var params = $('#form_tables_load').serialize();
            params+="&filename=" + current_project_name;

            //console.log(params);
            var url=$('#form_tables_save').attr('action');
            $.ajax({
                method: "POST",
                url: url,
                data: params,
                async: false
            })
            .done(function( html ) {
                $('#container').append(html);
                initTables();

                $('#container .table').each(function() {
                    var id=$(this).attr('id');
                    $('#tables .navitable input[value=' + id + ']').prop( "checked", true );
                });
            });
            refreshProject();
        }
        hideWait();
    });

    function refreshProject()
    {
        $('#projectname').html(current_project_name);
    }

    $('#container').mousemove(function(event) {
        if(tableselected!=null)
        {
            $('#container .helper').css({
                left: (event.pageX - tableselected_offset_start_left + $(this).scrollLeft()) + 'px',
                top: (event.pageY - tableselected_offset_start_top + $(this).scrollTop()) + 'px'
            });
        }
        if(tableresizer!=null)
        {
            var position = $(tableresizer).parent().position();
            $('#container .helper').css({
                width: (event.pageX - tableselected_offset_start_left + $(this).scrollLeft()) + 'px',
                height: (event.pageY - tableselected_offset_start_top + $(this).scrollTop()) + 'px'
            });
        }
    }).mouseup(function() {
        if(tableselected!=null) {

            var position = $('#container .helper').position();
            var left = Math.round((position.left + $(this).scrollLeft()) / 10) * 10;
            var top = Math.round((position.top + $(this).scrollTop()) / 10) * 10;

            $(tableselected).css({
                left: left + 'px',
                top: top + 'px'
            }).removeClass('selected');

            $('#container .helper').css({
                display: 'none'
            });

            tableselected = null;

            drawLines($(tableselected).attr('id'));
        }
        if(tableresizer!=null)
        {
            var width = $('#container .helper').width();
            var height = $('#container .helper').height();
            if(width<20)
                width = 20;
            if(height<40)
                height=40;
            var width = Math.round(width / 10) * 10;
            var height = Math.round(height / 10) * 10;

            var parent = $(tableresizer).parent();
            $(parent).css({
                width: width + 'px',
                height: height + 'px'
            }).removeClass('selected');

            $('#container .helper').css({
                display: 'none'
            });

            tableresizer=null;

            drawLines($(parent).attr('id'));
        }
    });

    initTables();
});


function displayWait()
{
    $('#popup_wait').css({'display':'block'});
    $('#popup_wait').css({
        "z-index": (tableselected_zindex + 100)
    });
}
function hideWait()
{
    $('#popup_wait').css({'display':'none'});
}

function getRelations()
{
    var params = $('#form_tables_relations').serialize();
    $('#container .table').each(function() {
        params+="&tables[]=" + $(this).attr('id');
    });
    //console.log(params);
    var url=$('#form_tables_relations').attr('action');
    $.ajax({
        method: "POST",
        url: url,
        data: params,
        async: false
    })
    .done(function( html ) {
        $('#container').append(html);
    });
    drawLines("");
}


var tableselected=null;
var tableresizer=null;
var tableselected_offset_start_top=-1;
var tableselected_offset_start_left=-1;
var tableselected_zindex=1;

function initTables()
{
    $('#container .table .header').mousedown(function(event) {
        if(event.which==1)
        {
            tableselected=$(this).parent();

            var position = $(tableselected).position();
            tableselected_offset_start_top = event.pageY - position.top;
            tableselected_offset_start_left = event.pageX - position.left;

            $(tableselected).css({
                "z-index": (tableselected_zindex+=2)
            }).addClass('selected');

            //display helper
            $('#container .helper').css({
                left: position.left + $('#container').scrollLeft() + "px",
                top: position.top + $('#container').scrollTop() + "px",
                width: $(tableselected).css("width" ),
                height: $(tableselected).css("height" ),
                display: 'block'
            });
        }
    });
    $('#container .table .resizer').mousedown(function(event) {
        if(event.which==1)
        {
            tableresizer=this;

            var position = $(tableresizer).parent().offset();
            tableselected_offset_start_top = position.top + $('#container').scrollTop();
            tableselected_offset_start_left = position.left + $('#container').scrollLeft();

            var parent = $(tableresizer).parent();
            $(parent).css({
                "z-index": (tableselected_zindex+=2)
            }).addClass('selected');

            $('#container .helper').css({
                left: $(parent).css("left"),
                top: $(parent).css("top"),
                width: $(parent).css("width"),
                height: $(parent).css("height"),
                display: 'block'
            });
        }
    });
    $('#container .content').scroll(function() {
        drawLines($(this).attr('id'));
    });

    getRelations();
}





function drawLines(onlyid)
{
    onlyid = onlyid || "";

    $('svg.polyline').each(function() {
        var id = $(this).attr('id');
        var from_table_name = $(this).attr('data-from_table');
        var to_table_name   = $(this).attr('data-to_table');


        if(from_table_name==onlyid || to_table_name==onlyid || onlyid=="")
        {
            var from_column_name = $(this).attr('data-from_column');
            var to_column_name   = $(this).attr('data-to_column');

            var container = $('#container');
            var from_table = $('#'+from_table_name);
            var to_table = $('#'+to_table_name);
            var from_column = $('#'+from_table_name+"__"+from_column_name);
            var to_column = $('#'+to_table_name+"__"+to_column_name);
            var from_text = $('#' + id + "_fromtext");
            var to_text = $('#' + id + "_totext");

            var from_table_pos=from_table.position();
            var to_table_pos  =to_table.position();
            var from_column_pos=from_column.position();
            var to_column_pos  =to_column.position();

            var header_height=20;
            var column_padding=10;

            var x_from = container.scrollLeft() + from_table_pos.left;
            var x_to   = container.scrollLeft() + to_table_pos.left;

            //pos svg
            var svgleft=0;
            var svgtop=0;
            var svgwidth=0;
            var svgheight=0;
            var svgpadding=20;
            var svgpadding_left=false;
            var svgpadding_right=false;

            //0 = left top, 1 = right top, 2 = right bottom, 3 = left bottom
            var from_text_bottom=false;
            var to_text_bottom=false;
            var from_text_right=false;
            var to_text_right=false;

            //left,width
            //find smallest distance
            var p1 = x_from;
            var p2 = Math.abs(x_from + from_table.width());
            var p3 = x_to;
            var p4 = Math.abs(x_to + to_table.width());

            var d1 = (p1>p3?p1-p3:p3-p1);
            var d2 = (p2>p3?p2-p3:p3-p2);
            var d4 = (p4>p1?p4-p1:p1-p4);
            var x_left_to_right=true;
            var y_top_to_bottom=true;
            var reverse_point1_point2=false;
            var reverse_point3_point4=false;

            if(d1 <= d2 && d1 <= d4)
            {
                //d1
                console.log('d1');
                if(p1 < p3)
                {
                    console.log('l1 ok');
                    svgleft = p1;
                    svgwidth = p3 - svgleft;
                    svgpadding_left=true;
                    reverse_point1_point2=true;
                    to_text_right=true;
                }
                else
                {
                    console.log('l2 ok');
                    svgleft = p3;
                    svgwidth = p1 - svgleft;
                    x_left_to_right=false;
                    svgpadding_left=true;
                    reverse_point3_point4=true;
                    from_text_right=true;
                }
            }
            else if(d2 <= d1 && d2 <= d4)
            {
                //d2
                console.log('d2');
                if(p2 < p3)
                {
                    console.log('l3 ok');
                    svgleft = p2;
                    svgwidth = p3 - svgleft;
                    to_text_right=true;
                }
                else
                {
                    console.log('l4 ok');
                    svgleft = p3;
                    svgwidth = p2 - svgleft;
                    x_left_to_right=false;
                    svgpadding_left=true;
                    svgpadding_right=true;
                    reverse_point1_point2=true;
                    reverse_point3_point4=true;
                    from_text_right=true;
                }
            }
            else
            {
                //d4
                console.log('d4');
                if(p1 < p4)
                {
                    console.log('l7 ok');
                    svgleft = p1;
                    svgwidth = p4 - svgleft;
                    svgpadding_left=true;
                    svgpadding_right=true;
                    reverse_point1_point2=true;
                    reverse_point3_point4=true;
                    to_text_right=true;
                }
                else
                {
                    console.log('l8 ok');
                    svgleft = p4;
                    svgwidth = p1 - svgleft;
                    x_left_to_right=false;
                    from_text_right=true;
                }
            }

            var y_from = container.scrollTop()  + from_table_pos.top;
            var y_to   = container.scrollTop()  + to_table_pos.top;
            if(from_column_pos.top < 0)
            {
                y_from += header_height;
            }
            else if(from_column_pos.top + from_column.height() > from_column.parent().height())
            {
                y_from += from_column.parent().height() + header_height;
            }
            else
            {
                y_from += from_column_pos.top + header_height + column_padding;
            }
            if(to_column_pos.top < 0)
            {
                y_to += header_height;
            }
            else if(to_column_pos.top + to_column.height() > to_column.parent().height())
            {
                y_to += to_column.parent().height() + header_height;
            }
            else
            {
                y_to += to_column_pos.top + header_height + column_padding;
            }

            //top, height
            if(y_from < y_to)
            {
                svgtop = y_from;
                svgheight = y_to - y_from;
                y_top_to_bottom=true;
                to_text_bottom=true;
            }
            else
            {
                svgtop = y_to;
                svgheight = y_from - y_to;
                y_top_to_bottom=false;
                from_text_bottom=true;
            }


            //padding
            if(svgpadding_left)
            {
                svgleft -= svgpadding;
                svgwidth += svgpadding;
            }
            if(svgpadding_right)
            {
                svgwidth += svgpadding;
            }

            //pos svg
            $(this).css({
                left: svgleft + 'px',
                top: svgtop + 'px',
                width: svgwidth + 'px',
                height: svgheight + 'px',
                display:'block'
            });
            $(this).css({
                "z-index": (tableselected_zindex - 1)
            });

            //pos line
            var point1 = (x_left_to_right?0:svgwidth) + "," + (y_top_to_bottom?0:svgheight);
            var point2 = (x_left_to_right?(svgpadding<svgwidth?svgpadding:svgwidth):svgwidth-(svgpadding<svgwidth?svgpadding:svgwidth)) + "," + (y_top_to_bottom?0:svgheight);
            var point3 = (!x_left_to_right?(svgpadding<svgwidth?svgpadding:svgwidth):svgwidth-(svgpadding<svgwidth?svgpadding:svgwidth)) + "," + (!y_top_to_bottom?0:svgheight);
            var point4 = (!x_left_to_right?0:svgwidth) + "," + (!y_top_to_bottom?0:svgheight);
            var points = "";
            points += (!reverse_point1_point2?"M" + point1 + " C" + point2:"M" + point2 + " C" + point1);
            points += " ";
            points += (!reverse_point3_point4?point3 + " " + point4:point4 + " " + point3);

            var polyline = $(this).find('.line');
            //$(polyline).attr('points',points);
            $(polyline).attr('d',points);


            //text
            var x_from_text = svgleft;
            var y_from_text = svgtop;
            if(from_text_right)
                x_from_text += svgwidth - 20;
            if(from_text_bottom)
                y_from_text += svgheight - 20;
            from_text.css({
                left: x_from_text + 'px',
                top: y_from_text + 'px',
                display:'block'
            });
            from_text.css({
                "z-index": (tableselected_zindex - 1)
            });

            var x_to_text = svgleft;
            var y_to_text = svgtop;
            if(to_text_right)
                x_to_text += svgwidth - 20;
            if(to_text_bottom)
                y_to_text += svgheight - 20;
            to_text.css({
                left: x_to_text + 'px',
                top: y_to_text + 'px',
                display:'block'
            });
            to_text.css({
                "z-index": (tableselected_zindex - 1)
            });
        }
    });
}
