<?php
// Include SweetAlert2 library
// Make sure to include SweetAlert2 CSS and JS files in your project

require_once 'conn-d.php';

if (isset($_POST['edit'])) {
    $invoiceId = $_POST['invoice_number'];
    $editedTotalAmount = $_POST['editedTotalAmount'];
    $totalAmountAfterPercentage = $_POST['totalAmountAfterPercentage'];

    // Perform the update in the database
    $updateSql = "UPDATE invoices SET total_amount = '$editedTotalAmount', total_amount_after_percentage = '$totalAmountAfterPercentage' WHERE id = $invoiceId";
    $updateResult = mysqli_query($conn, $updateSql);

    if ($updateResult) {
        // Item updated successfully
        header("Location: $_SERVER[HTTP_REFERER]"); // Redirect back to the previous page
        exit();
    } else {
        // Error updating item
        echo "Error updating item: " . mysqli_error($conn);
    }
} elseif (isset($_POST['delete'])) {
    // If the delete button is clicked, show SweetAlert2 confirmation dialog
    $invoiceIdToDelete = $_POST['invoice_number'];

    // JavaScript code for SweetAlert2 confirmation
    echo "
        <script>
            // Show SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                // If user clicks 'Yes', proceed with the deletion
                if (result.isConfirmed) {
                    // Redirect to the delete_item.php page
                    window.location.href = 'delete_item.php?invoice_number=$invoiceIdToDelete';
                } else {
                    // Redirect back to the same page if user clicks 'No'
                    window.location.href = '$_SERVER[HTTP_REFERER]';
                }
            });
        </script>
    ";
}
?>
