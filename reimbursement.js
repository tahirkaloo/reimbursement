$(document).ready(function() {
  $('#reimbursement-form').on('input', calculateReimbursement);
  $('#add-row').on('click', addRow);

  function calculateReimbursement() {
    var totalMileage = 0;
    var mileageReimbursement = 0;
    var parkingTolls = parseFloat($('[name="tolls-parking[]"]').val()) || 0;

    $('#mileageTable tbody tr').each(function() {
      var mileage = parseFloat($(this).find('input[name="mileage[]"]').val()) || 0;
      totalMileage += mileage;
    });

    var tollsParking = 0;

    $('input[name="tolls-parking[]"]').each(function() {
      var value = parseFloat($(this).val()) || 0;
      tollsParking += value;
    });

    mileageReimbursement = totalMileage * 0.655;
    var totalReimbursement = mileageReimbursement + tollsParking;

    $('#total-mileage').text(totalMileage.toFixed(2));
    $('#mileage-reimbursement').text(mileageReimbursement.toFixed(2));
    $('#total-reimbursement').text(totalReimbursement.toFixed(2));
  }

  function addRow() {
    var row =
      '<tr>' +
      '<td><input type="date" name="date[]" required=""></td>' +
      '<td><input type="number" name="tolls-parking[]" min="0" step="any"></td>' +
      '<td><input type="text" name="origin[]"></td>' +
      '<td><input type="text" name="destination[]"></td>' +
      '<td><input type="checkbox" name="multiple-stops[]" value="yes"></td>' +
      '<td><input type="number" name="mileage[]" min="0" step="any" required=""></td>' +
      '<td><input type="text" name="purpose[]"></td>' +
      '<td><button type="button" class="btn btn-primary remove-row">Remove</button></td>' +
      '</tr>';

    $('#mileageTable tbody').append(row);
    calculateReimbursement(); // Calculate reimbursement after adding a row
  }

  calculateReimbursement();

  // Remove row event delegation
  $('#mileageTable').on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
    calculateReimbursement(); // Recalculate reimbursement after row removal
  });
});

