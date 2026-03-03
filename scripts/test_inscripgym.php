<?php
use App\Models\Inscripgym;

echo "Checking Inscripgym data...\n";
$count = Inscripgym::count();
echo "Total records: " . $count . "\n";

if ($count > 0) {
    $first = Inscripgym::first();
    echo "First record:\n";
    print_r($first->toArray());
} else {
    echo "No records found.\n";
}
