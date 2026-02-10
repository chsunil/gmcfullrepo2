// File: acf-math-calc.js

(function($){
  
  // Wait for ACF to initialize the fields - use append instead of ready to avoid conflicts
  acf.addAction('append', function($el){
    
    // Find only math calc fields within this field group
    $el.find('.acf-math-calc').each(function(){
      var $field   = $(this);
      var formula  = $field.data('formula') || '';
      
      // Skip if no formula
      if (!formula) return;
      
      var names    = (formula.match(/\b\w+\b/g) || [])
                       .filter(function(n){ return n !== 'Math'; });
      
      function update(){
        var expr = formula;
        names.forEach(function(name){
          var f = acf.getField(name);
          var v = f ? f.$input().val() : 0;
          // replace all exact occurrences of name with its value
          expr = expr.replace(new RegExp('\\b'+name+'\\b','g'), parseFloat(v) || 0);
        });
        try {
          var result = Function('"use strict"; return (' + expr + ')')();
          $field.val( result.toFixed(2) );
        } catch(e) {
          $field.val('Error');
        }
      }
      
      // Bind update to each dependent field
      names.forEach(function(name){
        var f = acf.getField(name);
        if (f) {
          f.$input().on('input change', update);
        }
      });
      
      // Initial calculation
      setTimeout(update, 100);
    });
    
  });
  
})(jQuery);
