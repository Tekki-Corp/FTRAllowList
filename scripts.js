// function deleteRow(btn) {
//     var row = btn.parentNode.parentNode;
//     row.parentNode.removeChild(row);
// }

function markForDeletion(btn, index) {
    if (confirm("Are you sure you want to delete this row?")) {
        var row = btn.parentNode.parentNode;
        Array.from(row.cells).forEach(cell => cell.classList.add('strike-through'));
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete[]';
        input.value = index;
        row.appendChild(input);

        // Ensure the new entry fields are not required when deleting an entry
        toggleNewEntryRequired(false);
    }
}

function enableEdit(btn) {
    var row = btn.parentNode.parentNode;
    var inputs = row.getElementsByTagName('input');

    // Check if any input is empty when trying to exit edit mode
    for (var i = 0; i < inputs.length; i++) {
        if (!inputs[i].readOnly && inputs[i].value.trim() === "") {
            alert("Please fill out all fields.");
            return; // Exit the function without toggling edit mode off if any field is empty
        }
    }

    // Toggle edit mode for all inputs in the row
    for (var i = 0; i < inputs.length; i++) {
        // Only validate IP format if the field is an IP field
        if (!inputs[i].readOnly && inputs[i].name.includes('ip')) {
            if (!isValidIP(inputs[i].value)) {
                alert("Enter a valid IPv4 address (e.g., 0.0.0.0)");
                return; // Exit the function without toggling edit mode off
            }
        }

        inputs[i].readOnly = !inputs[i].readOnly; // Toggle readOnly state.
        inputs[i].classList.toggle('edit-mode', !inputs[i].readOnly);

        // Additional logic when enabling edit mode
        if (!inputs[i].readOnly) {
            toggleNewEntryRequired(false);
        }
    }
}

function isValidIP(ip) {
    const regex = new RegExp('^\\d{1,3}(\\.\\d{1,3}){3}$');
    return regex.test(ip) && ip.split('.').every(segment => {
        const num = parseInt(segment, 10);
        return num >= 0 && num <= 255;
    });
}

function toggleNewEntryRequired(makeRequired) {
    document.querySelector("input[name='newName[]']").required = makeRequired;
    document.querySelector("input[name='newIp[]']").required = makeRequired;
}

// Call this function on form submission
document.getElementById('editForm').addEventListener('submit', function () {
    toggleNewEntryRequired(true); // Re-enable required if they are submitting a new entry
});

document.addEventListener("DOMContentLoaded", function () {
    var alerts = document.querySelectorAll('.auto-dismiss');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.display = 'none';
        }, 10000);
    });
});

var rowToDelete = null;
function showDeleteConfirmation(button) {
    rowToDelete = button.closest('tr'); // Find the closest table row
    $('#deleteConfirmationModal').modal('show');
}

function deleteRow() {
    if (rowToDelete) {
        rowToDelete.remove(); // Remove the row
        $('#deleteConfirmationModal').modal('hide'); // Hide the modal
    }
}