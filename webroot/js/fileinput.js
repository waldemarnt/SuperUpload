/* ===========================================================
 * Bootstrap: fileinput.js v3.0.0-p7
 * http://jasny.github.com/bootstrap/javascript.html#fileinput
 * ===========================================================
 * Copyright 2012 Jasny BV, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

+function ($) { "use strict";

  var isIE = window.navigator.appName == 'Microsoft Internet Explorer'

  // FILEUPLOAD PUBLIC CLASS DEFINITION
  // =================================

  var Fileupload = function (element, options) {
    this.$element = $(element)
    this.$input = this.$element.find(':file')
    if(document.getElementById('image_exist') != null){
    this.have_image(this.$element,document.getElementById('image_name').value)
    }
    if (this.$input.length === 0) return
   if(window.options != undefined){
    this.name = this.$input.attr('name') || options.name
  }
    this.$hidden = this.$element.find('input[type=hidden][name="'+this.name+'"]')
    if (this.$hidden.length === 0) {
      this.$hidden = $('<input type="hidden" />')
      this.$element.prepend(this.$hidden)
    }

    this.$preview = this.$element.find('.fileinput-preview')
    var height = this.$preview.css('height')
    if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)

    this.original = {
      exists: this.$element.hasClass('fileinput-exists'),
      preview: this.$preview.html(),
      hiddenVal: this.$hidden.val()
    }

    this.listen()
  }

  Fileupload.prototype.listen = function() {

    this.$input.on('change.bs.fileinput', $.proxy(this.change, this))
    $(this.$input[0].form).on('reset.bs.fileinput', $.proxy(this.reset, this))

    this.$element.find('[data-trigger="fileinput"]').on('click.bs.fileinput', $.proxy(this.trigger, this))
    this.$element.find('[data-dismiss="fileinput"]').on('click.bs.fileinput', $.proxy(this.clear, this))
  },

  Fileupload.prototype.change = function(e) {

   this.send_to_server(e.target.files[0],$(e.target).data('position'));

    if (e.target.files === undefined) e.target.files = e.target && e.target.value ? [ {name: e.target.value.replace(/^.+\\/, '')} ] : []
    if (e.target.files.length === 0) return

    this.$hidden.val('')
    this.$hidden.attr('name', '')
    this.$input.attr('name', this.name)

    var file = e.target.files[0]

    if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
      var reader = new FileReader()
      var preview = this.$preview
      var element = this.$element

      reader.onload = function(re) {
        var $img = $('<img>').attr('src', re.target.result)
        e.target.files[0].result = re.target.result
        element.find('.fileinput-filename').text(file.name)

        // if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
        if (preview.css('max-height') != 'none') $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10)  - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))
        preview.html($img)
        element.addClass('fileinput-exists').removeClass('fileinput-new')

        element.trigger('change.bs.fileinput', e.target.files)
      }

      reader.readAsDataURL(file)
    } else {
      this.$element.find('.fileinput-filename').text(file.name)
      this.$preview.text(file.name)

      this.$element.addClass('fileinput-exists').removeClass('fileinput-new')

      this.$element.trigger('change.bs.fileinput')
    }
  },
  Fileupload.prototype.have_image = function(element,name) {
      element.addClass('fileinput-exists').removeClass('fileinput-new')

},

  Fileupload.prototype.clear = function(e) {
    if (e) e.preventDefault()

    this.$hidden.val('')
    this.$hidden.attr('name', this.name)
    this.$input.attr('name', '')

    //ie8+ doesn't support changing the value of input with type=file so clone instead
    if (isIE) {
      var inputClone = this.$input.clone(true);
      this.$input.after(inputClone);
      this.$input.remove();
      this.$input = inputClone;
    } else {
      this.$input.val('')
    }

    this.$preview.html('')
    this.$element.find('.fileinput-filename').text('')
    this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

    if (e !== false) {
      this.$input.trigger('change')
      this.$element.trigger('clear.bs.fileinput')
    }
  },

  Fileupload.prototype.send_to_server = function(file,position) {
   var response_server;
  var local_path = document.getElementById('local_path').value;
  var id = document.getElementById('id').value;
  var model = document.getElementById('model').value;
  var thumb_width = document.getElementById('thumb_width').value;
  var thumb_height = document.getElementById('thumb_height').value;


  var formData = new FormData();

  formData.append('image',file);
  formData.append('id',id);
  formData.append('model',model);
  formData.append('is_single',position);
  formData.append('thumb_height',thumb_height);
  formData.append('thumb_width',thumb_width);

    var http = new XMLHttpRequest();
    http.open("POST", local_path+"upload/uploads/save_single.json", true);

    http.onreadystatechange = function() {
    if(http.readyState == 4 && http.status == 200) {
    var value = JSON.parse(http.responseText);
    if(value.success == true){
    toastr.success('Imagem Atualizada!');
    }else if(value.success == 'size_error'){
    toastr.error('Imagem Incorreta tamanho minimo: '+thumb_width+"x"+thumb_height);
    $("#remove_image").trigger("click");
    }
    //$('#modal-crop-single').modal('show',{backdrop: 'static'});

  }
}
http.send(formData);
},

$(document).ready(function() {

$("body").on('click', '#crop', function() {

  cropFile($(this).data('filename'),$(this).data('position'),$(this).data('model'))
});

// $("body").on('click', '#remove_image', function() {
//   alert('diasjdiasjd');
//   //removeFile($(this).data('filename'),$(this).data('position'),$(this).data('model'))
// });


  // $('#crop').click( function(e){
  //     if(document.getElementById('single_image').value.length <5){
  //       var file = document.getElementById('single_image').name;
  //       cropFile(file);
  //     }else{
  //       var file = document.getElementById('single_image').value;
  //       cropFile(file);
  //             }
  // });
    $('#modal-crop-single').on('hide.bs.modal', function (e) {
    //JcropAPI = $('#jcrop-4').data('Jcrop');
      //alert(e);
    $("#saveCropSingle").unbind('click');
//    $JcropAPI.setImage(null);

    });
  //  $('#modal-crop-single').on('show.bs.modal', function (e) {
    //$JcropAPI = $('#jcrop-4').data('Jcrop');

//    });



});


//load image to crop
   function cropFile(file,position,model){
  var local_path = document.getElementById('local_path').value;

    var formData = new FormData();
    file = file.replace('C:\\fakepath\\','');
    formData.append("filename",file);
    formData.append("model",model);
    formData.append("media_id",document.getElementsByName("id")[0].value);
    formData.append("position",position);

    var http = new XMLHttpRequest();
    http.open("POST", local_path+"upload/uploads/crop.json", true);
    //http.setRequestHeader('Content-Type', 'application/json');
    http.setRequestHeader("Content-length", formData.length);
    http.setRequestHeader("Connection", "close");


     http.onreadystatechange = function() {

    //Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
    var value = JSON.parse(http.responseText);
    var completPathUrl = value.value.Upload.path;
    var deleteUrl = local_path+"/upload/uploads/add";
    var deletePostUrl = local_path+"/upload/uploads/crop.json";
    document.getElementById('imgId').value=value.value.Upload.id;
    document.getElementById('crop_model').value=value.value.Upload.model;


    //alert( document.getElementById('jcrop-3').src="../"+completPathUrl+"?"+parseInt(Math.random()*11));
    document.getElementById('jcrop-4').src=local_path+completPathUrl+"?"+parseInt(Math.random()*11);
    //document.getElementById('preview').src=local_path+completPathUrl+"?"+parseInt(Math.random()*11);

    var JcropAPI = $('#jcrop-4').data('Jcrop');
    $('#modal-crop-single').modal('show',{backdrop: 'static'});

    if(JcropAPI){
        JcropAPI.setImage(document.getElementById('jcrop-4').src);
      }
    $('#modal-crop-single').on('show.bs.modal', function (e) {
    JcropAPI = $('#jcrop-4').data('Jcrop');
    });
    showCrop(value);
    getCropData(completPathUrl,deleteUrl);
   // $("#saveCrop").bind('click');
    //});

    //$('#modal-6').on('hide.bs.modal', function (e) {
      //alert(e);
    //$("#saveCrop").unbind('click');
    //});
  }
}

http.send(formData);
    }

  function showCrop(value){

  // Example 1 - Simple case
  $("#jcrop-1").Jcrop({}, function()
  {
    this.setSelect([580, 310, 320, 140]);
  });

  // Example 2 - Coordinates Fetch
  $("#jcrop-2").Jcrop({
    onSelect: showCoords,
    onChange: showCoords
  });

  function showCoords(c)
  {
    $('#jc2-x1').val(c.x);
    $('#jc2-y1').val(c.y);
    $('#jc2-x2').val(c.x2);
    $('#jc2-y2').val(c.y2);
    $('#jc2-w').val(c.w);
    $('#jc2-h').val(c.h);
  }

  // Example 3 - crop the thumbnail
  var img_3_w = $("#jcrop-4").width(),
    img_3_h = $("#jcrop-4").height();

  $('#jcrop-4').Jcrop({
    //get original file size from database
    trueSize: [value.value.Upload.width,value.value.Upload.height],
     setSelect:setSizes(),
      boxWidth: 500,
      boxHeight: 400,
      minSize:[$('#thumb_width').val(),$('#thumb_height').val()],
      aspectRatio: $('#thumb_width').val()/$('#thumb_height').val(),
      onSelect: showPreview
  });
  function setSizes(){
  if(value.value.Upload.x === null){

  return  [200, 200, 120, 40];
  }else{
    return [value.value.Upload.x, value.value.Upload.y, value.value.Upload.x2, value.value.Upload.y2];

  }
  }
  function showPreview(coords)
  {

    var rx = 150 / coords.w;
    var ry = 150 / coords.h;
    updateCoords(coords);
    // $('#preview').css({
    //   width: Math.round(rx * value.value.Upload.width) + 'px',
    //   height: Math.round(ry * value.value.Upload.height) + 'px',
    //   marginLeft: '-' + Math.round(rx * coords.x) + 'px',
    //   marginTop: '-' + Math.round(ry * coords.y) + 'px'
    // });
  }


  function updateCoords(c)
  {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#x2').val(c.x2);
    $('#y2').val(c.y2);
    $('#w').val(c.w);
    $('#h').val(c.h);

  };


};

//get crop data, and save cropped
function getCropData(file,getUrl){
  var formData = new FormData();
  $('#saveCropSingle').click( function(){
    formData.append("image",file);
    formData.append("w",$('#w').val());
    formData.append("h",$('#h').val());
    formData.append("x",$('#x').val());
    formData.append("y",$('#y').val());
    formData.append("x2",$('#x2').val());
    formData.append("y2",$('#y2').val());
    formData.append("crop_model",$('#crop_model').val());


    getUrl = getUrl.replace("/add", "/savecropped.json");
    var http = new XMLHttpRequest();
    http.open("POST", getUrl, true);
    //http.setRequestHeader('Content-Type', 'application/json');
    http.setRequestHeader("Content-length", formData);
    http.setRequestHeader("Connection", "close");

    http.onreadystatechange = function() {
    //Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
    //var value = JSON.parse(http.responseText);
    $('#modal-crop-single').modal('hide');
    $("#saveCropSingle").unbind('click');
    toastr.success('Imagem Editada!');

  }
}
http.send(formData);

  });

}


  Fileupload.prototype.reset = function() {
    this.clear(false)

    this.$hidden.val(this.original.hiddenVal)
    this.$preview.html(this.original.preview)
    this.$element.find('.fileinput-filename').text('')

    if (this.original.exists) this.$element.addClass('fileinput-exists').removeClass('fileinput-new')
     else this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

    this.$element.trigger('reset.bs.fileinput')
  },

  Fileupload.prototype.trigger = function(e) {
    this.$input.trigger('click')
    e.preventDefault()
  }


  // FILEUPLOAD PLUGIN DEFINITION
  // ===========================

  $.fn.fileinput = function (options) {
    return this.each(function () {
      var $this = $(this)
      , data = $this.data('fileinput')
      if (!data) $this.data('fileinput', (data = new Fileupload(this, options)))
      if (typeof options == 'string') data[options]()
    })
  }

  $.fn.fileinput.Constructor = Fileupload


  // FILEUPLOAD DATA-API
  // ==================

  $(document).on('click.fileinput.data-api', '[data-provides="fileinput"]', function (e) {
    var $this = $(this)
    if ($this.data('fileinput')) return
    $this.fileinput($this.data())

    var $target = $(e.target).closest('[data-dismiss="fileinput"],[data-trigger="fileinput"]');
    if ($target.length > 0) {
      e.preventDefault()
      $target.trigger('click.bs.fileinput')
    }
  })

}(window.jQuery);
