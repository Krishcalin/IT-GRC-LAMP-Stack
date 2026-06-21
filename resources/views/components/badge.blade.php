@props(['value' => '', 'map' => []])
@php
    $v = trim((string) $value);
    $palette = [
        'Implemented' => 'green', 'Fully Implemented' => 'green', 'Fully' => 'green', 'Conformant' => 'green',
        'Approved' => 'green', 'Completed' => 'green', 'Resolved' => 'green', 'Closed' => 'green', 'Done' => 'green',
        'Active' => 'green', 'On Track' => 'green', 'On Target' => 'green', 'Achieved' => 'green', 'Compliant' => 'green',
        'Yes' => 'green', 'Verified' => 'green',
        'In Progress' => 'blue', 'Triaged' => 'blue', 'Under Review' => 'blue', 'In Treatment' => 'blue',
        'Submitted' => 'blue', 'Reviewed' => 'blue', 'Onboarding' => 'blue', 'Observation' => 'blue', 'OFI' => 'blue',
        'Partially' => 'amber', 'Partially Conformant' => 'amber', 'Partial' => 'amber', 'At Risk' => 'amber',
        'Near Target' => 'amber', 'Open' => 'amber', 'New' => 'amber', 'Medium' => 'amber', 'Minor NC' => 'amber',
        'Blocked' => 'amber', 'Overdue' => 'red',
        'Critical' => 'red', 'High' => 'red', 'Nonconformant' => 'red', 'Non-Compliant' => 'red', 'Off Target' => 'red',
        'Missed' => 'red', 'Major NC' => 'red', 'No' => 'red', 'Cancelled' => 'red',
        'Planned' => 'gray', 'Draft' => 'gray', 'Not Started' => 'gray', 'Not Assessed' => 'gray', 'Assigned' => 'gray',
        'Not Implemented' => 'gray', 'Not Applicable' => 'gray', 'N/A' => 'gray', 'Low' => 'gray', 'Inactive' => 'gray',
        'Offboarded' => 'gray', 'Retired' => 'gray', 'Exempt' => 'gray',
        'Organizational' => 'indigo', 'People' => 'cyan', 'Physical' => 'amber', 'Technological' => 'violet',
        'Govern' => 'indigo', 'Identify' => 'cyan', 'Protect' => 'green', 'Detect' => 'amber', 'Respond' => 'red', 'Recover' => 'violet',
    ];
    $color = $map[$v] ?? $palette[$v] ?? 'gray';
    $classes = [
        'green' => 'bg-green-100 text-green-700', 'blue' => 'bg-blue-100 text-blue-700',
        'amber' => 'bg-amber-100 text-amber-800', 'red' => 'bg-red-100 text-red-700',
        'gray' => 'bg-gray-100 text-gray-600', 'indigo' => 'bg-indigo-100 text-indigo-700',
        'cyan' => 'bg-cyan-100 text-cyan-700', 'violet' => 'bg-violet-100 text-violet-700',
    ][$color];
@endphp
@if($v !== '')<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $classes }}">{{ $v }}</span>@endif
