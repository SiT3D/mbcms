
(function () { var ADMIN_CONSTRUCTOR_ADR = '/viewer'; 
 $('document').ready(function ()
 {  setTimeout(function()
 {  if (site.configuration.is_static_templates === true)
  {   return;  } 
  if (!/\/viewer+/.test(location.pathname))
  {   $('<div/>')
   .appendTo('body')
   .css({position: 'fixed', left: 0, top: 0, width: 20, height: 20, opacity: 0.2, background: '#eee', borderRadius: '50%',   zIndex: 12, textAlign: 'center', lineHeight: '20px', cursor: 'pointer'})
   .click(function ()
   {    location.href = location.origin + ADMIN_CONSTRUCTOR_ADR + location.pathname;   })
   .hover(function ()
   {    $(this).css({opacity: 1});   }, function ()
   {    $(this).css({opacity: 0.2});   })
   .text('A')
  ;  }  else
  {   $('<div/>')
   .appendTo('body')
   .css({position: 'fixed', left: 0, top: 0, width: 20, height: 20, opacity: 0.2, background: '#eee', borderRadius: '50%',   zIndex: 12, textAlign: 'center', lineHeight: '20px', cursor: 'pointer'})
   .click(function ()
   {    var p = location.pathname.replace(ADMIN_CONSTRUCTOR_ADR, '');   location.href = location.origin + p;   })
   .hover(function ()
   {    $(this).css({opacity: 1});   }, function ()
   {    $(this).css({opacity: 0.2});   })
   .text('S')
  ;  }  }, 0); 
 }); })(); 



new event.site.load().listen(function () {
 if (!isset(site, 'configuration', 'is_static_templates'))
 {
 return;
 }


 if (site.configuration.is_static_templates == 'live')
 {
 $(document)
  .keydown(function (e)
  {
  if (e.keyCode == KEY_F5 && !e.ctrlKey)
  {
   site.ajax(
   {
    data: {
    class: 'MBCMS\\template->autogenerate_static',
    idTemplate: site.configuration.idTemplate,
    },
    success: function ()
    {
    location.reload();
    }
   }
   );
   return false;
  }
  else if (e.keyCode == KEY_F5 && e.ctrlKey)
  {
   site.ajax(
   {
    data: {
    class: 'MBCMS\\template->destroy_static_view',
    idTemplate: site.configuration.idTemplate,
    },
    success: function ()
    {
    location.reload();
    }
   }
   );
   return false;
  }
  })
 ;
 }
});