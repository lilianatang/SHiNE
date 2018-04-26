/*
This file contains all the code associated with the table used in the family requirements page
*/

// Initiate the validator for number of students
jsGrid.validators.students = { 
  message: "Invalid number of students.", 

  validator: function(value) { 
    
    // Make sure that the value is a number, if it isn't it must be the word default
    if ( ( isNaN(value) && (value == "default") || ( !isNaN(value) && parseInt(value) > 0 ) ) ) 
    {
      return true;
    }

    return false;
  }
}; 

jsGrid.validators.hours = { 
  message: "Invalid number of hours.", 

  validator: function(value) { 
    
    // Make sure that the value is a number, if it isn't it must be the word default
    if (  !isNaN(value) && parseFloat(value) > 0  ) 
    {
      return true;
    }

    return false;
  }
}; 

var requirement_data = [];

// Retrieve the requirements from the database and display the grid
$.get('get_family_requirements', function (data) 
{
  requirement_data = [];
  data = data.split(",");
  data.pop();

  for (var i = 0; i < data.length; i ++)
  {
    var values = data[i].split(" ");

    if (values[0] != "default")
    {
      requirement_data.push( { "Number of Students": values[0], "Required Hours": values[1], deletable: true, editable: true, id: values[2]});
    }
    else
    {
      requirement_data.push( { "Number of Students": values[0], "Required Hours": values[1], deletable: false, editable: true, id: values[2]});
    }

  }

  // This allows for values in the table to be represented as decimals (floats)
  create_decimal_field();

  initGrid();

  $(".jsgrid-pager").css("text-align", "center");

});


/*
* This method creates the grid including the data from the database 
*/
var initGrid = function()
{

  // Initiate the grid
  $("#jsGrid").jsGrid({

      width: "500px",

      inserting: true,
      editing: true,
      sorting: true,
      paging: false,

      // Insert data we got above
      data: requirement_data,

      deleteConfirm: "Do you really want to delete this row?",

      // Create the columns
      fields: [
          { name: "Number of Students", type: "text", width: 10, validate: "required",  validate: "students", align: "center"}, 
          { name: "Required Hours", type: "decimal", width: 10, validate: "required", validate: "hours", align: "center" },
          { type: "control", width: 20, align: "center",  

          // Add the edit and delete buttons depending on the row specifications
          itemTemplate: function(value, item) {
              var $result = $([]);

              if(item.editable) {
                  $result = $result.add(this._createEditButton(item));
              }

              if(item.deletable) {
                  $result = $result.add(this._createDeleteButton(item));
              }

              return $result;
            } 
          }
      ],

       onItemInserting: function(args) 
       {
        //deletable: true, editable: true, id: values[2]}
        args.item["deletable"] = true;
        args.item["editable"] = true;
       
       },

      // on done of controller.insertItem
      onItemInserted: function(args) 
      { 
       
        var inserted_item = args.item;
        
        if (!remove_duplicates()) //grid_contains(inserted_item)
        {
            // For some reason, it doesn't like taking inserted_item directly
            $.post("insert_family_requirement", {num: inserted_item['Number of Students'], hours: inserted_item['Required Hours'] }, 
                function(data) {  

                    if (data != null){
                     inserted_item['id'] = data;
                    }
                    else{
                     $("#jsGrid").jsGrid("deleteItem", inserted_item);
                      error_message("Unable to connect to server. Please try again later!");
                    }

                } 
            );
        }
        else
        {
            error_message("That number of students already exists!");
        }

      }, 

      // on done of controller.updateItem 
      onItemUpdated: function(args) 
      {
      
        var updated_item = args.item;
        if(!remove_duplicates())
        {
            $.post("update_family_requirement", {num: updated_item['Number of Students'], hours: updated_item['Required Hours'], id: updated_item['id'] }, 
              function (data) {
                if (data == false)
                {
                  error_message("Unable to connect to server. Please try again later!");
                }

              }
            );
        }
        else
        {
            error_message("That number of students already exists!");
        }

      },    

      // on done of controller.deleteItem
      onItemDeleted: function(args) 
      {
        var deleted_item = args.item;
       
        $.post("delete_family_requirement", {num: deleted_item['Number of Students'], hours: deleted_item['Required Hours'], id: deleted_item['id'] }, 

            function (data) {
              if (data == false)
              {
                 $("#jsGrid").jsGrid("insertItem", deleted_item);
                 error_message("Unable to connect to server. Please try again later!");
              }
              

            }

          );

      }   

  });

  $("#jsGrid").fadeIn("slow");
  $("#back_button").fadeIn("slow");

}

var error_message = function (message)
{
    $('#message').text(message).css('color', 'red').css("font-weight", 'normal');
}

/*
  This method removes duplicates from the tables (one of the rows where the number of students values are the same)
*/
var remove_duplicates = function ()
{
  var items_deleted = false;
  var data = $("#jsGrid").jsGrid("option", "data");

  // Temporarily turn off delete confirm so that the duplicate row can be deleted
  $("#jsGrid").jsGrid("option", "confirmDeleting", false); 

  var unique_items = [];

  for (var i = 0; i < data.length; i ++)
  {

    var d = String(data[i]["Number of Students"]);

    if ( unique_items.indexOf(d) != -1 )
    {      
      $("#jsGrid").jsGrid("deleteItem", data[i]);
      
      items_deleted = true;
    }
    else{
     unique_items.push(d);
    }
  }

  $("#jsGrid").jsGrid("option", "confirmDeleting", true);

  return items_deleted;
}

// This bit of code allows for the creation of a float number type 
// Source: https://github.com/tabalinas/jsgrid/issues/621#issuecomment-278904860
var create_decimal_field = function ()
{

    function DecimalField(config) {
            jsGrid.fields.number.call(this, config);
    }

    DecimalField.prototype = new jsGrid.fields.number({

        filterValue: function() {
            return this.filterControl.val()
                ? parseFloat(this.filterControl.val() || 0, 10)
                : undefined;
        },

        insertValue: function() {
            return this.insertControl.val()
                ? parseFloat(this.insertControl.val() || 0, 10)
                : undefined;
        },

        editValue: function() {
            return this.editControl.val()
                ? parseFloat(this.editControl.val() || 0, 10)
                : undefined;
        }
    });

    jsGrid.fields.decimal = jsGrid.DecimalField = DecimalField;

}