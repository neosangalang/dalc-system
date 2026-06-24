<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IEP - {{ $student->first_name }} {{ $student->last_name }}</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-['Inter'] bg-white text-slate-900 antialiased text-[13px] p-4 sm:p-8 max-w-5xl mx-auto">

    <div class="text-center mb-8">
        <h3 class="text-xl font-black text-slate-900 tracking-tight m-0 mb-1">Dream Achievers Learning Center PH</h3>
        <p class="text-[11px] font-bold text-slate-500 m-0 leading-relaxed">2 Masunurin St. Brgy. Sikatuna Quezon, City</p>
        <p class="text-[11px] font-bold text-slate-500 m-0 leading-relaxed">09369523782</p>
        <p class="text-[11px] font-bold text-slate-500 m-0 leading-relaxed">Dreamachieverslearningphil@gmail.com</p>
        
        <hr class="border-t-2 border-slate-900 my-5">
        
        <h4 class="text-base font-black tracking-wider text-slate-950 uppercase underline decoration-2 underline-offset-4 my-1">INDIVIDUALIZED EDUCATIONAL PROGRAM</h4>
        <h4 class="text-base font-black tracking-wider text-slate-950 uppercase underline decoration-2 underline-offset-4 my-1">S.Y. {{ date('Y') }}-{{ date('Y')+1 }}</h4>
    </div>

    <table class="w-full border-collapse mb-6">
        <tbody>
            <tr>
                <td class="w-[12%] font-extrabold text-slate-700 uppercase tracking-wider text-xs py-2.5 whitespace-nowrap">Child's Name:</td>
                <td class="w-[38%] font-black text-slate-900 border-b border-slate-900 py-2.5 px-2 text-sm">{{ $student->last_name }}, {{ $student->first_name }}</td>
                <td class="w-[12%] font-extrabold text-slate-700 uppercase tracking-wider text-xs py-2.5 pl-6 whitespace-nowrap">Class:</td>
                <td class="w-[38%] font-black text-slate-900 border-b border-slate-900 py-2.5 px-2 text-sm">SPED</td>
            </tr>
            <tr>
                <td class="w-[12%] font-extrabold text-slate-700 uppercase tracking-wider text-xs py-2.5 whitespace-nowrap">Age:</td>
                <td class="w-[38%] font-black text-slate-900 border-b border-slate-900 py-2.5 px-2 text-sm">{{ \Carbon\Carbon::parse($student->date_of_birth)->age ?? 'N/A' }} yrs old</td>
                <td class="w-[12%] font-extrabold text-slate-700 uppercase tracking-wider text-xs py-2.5 pl-6 whitespace-nowrap">DOB:</td>
                <td class="w-[38%] font-black text-slate-900 border-b border-slate-900 py-2.5 px-2 text-sm">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td class="w-[12%] font-extrabold text-slate-700 uppercase tracking-wider text-xs py-2.5 whitespace-nowrap">Parents:</td>
                <td colspan="3" class="font-black text-slate-900 border-b border-slate-900 py-2.5 px-2 text-sm">{{ $student->guardian->name ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    @forelse($student->iepGoals as $goal)
        <div class="border-2 border-slate-900 bg-slate-50 rounded-xl p-3.5 font-black text-sm text-slate-900 tracking-widest uppercase mt-8 shadow-sm">
            DOMAIN : {{ str_replace('-', ' - ', strtoupper($goal->domain)) }}
        </div>

        <table class="w-full border-collapse mt-4 border-2 border-slate-900 overflow-hidden shadow-sm">
            <thead class="bg-slate-100 border-b-2 border-slate-900">
                <tr>
                    <th scope="col" class="border-2 border-slate-900 px-5 py-3 w-1/2 text-left text-xs font-black text-slate-900 uppercase tracking-wider">OBJECTIVES</th>
                    <th scope="col" class="border-2 border-slate-900 px-5 py-3 w-1/2 text-left text-xs font-black text-slate-900 uppercase tracking-wider">ACTIVITIES</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-900">
                @php
                    $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                @endphp

                @foreach($subGoals as $subGoalText)
                    @php
                        $subGoalText = trim($subGoalText);
                        if (empty($subGoalText)) continue;

                        $objective = $subGoalText;
                        $activities = '';

                        if (preg_match('/ACTIVITIES:/i', $subGoalText)) {
                            $parts = preg_split('/ACTIVITIES:/i', $subGoalText);
                            $objective = preg_replace('/OBJECTIVE:/i', '', $parts[0]);
                            $activities = $parts[1];
                        }

                        $objective = trim($objective);
                        $activities = trim($activities);

                        $objective = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $objective);
                        $activities = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $activities);

                        $activities = str_replace(['[ ]', '[x]', '[X]', '[]'], '•', $activities);
                    @endphp

                    @if(!empty($objective))
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="border-2 border-slate-900 p-5 text-slate-800 text-sm font-medium align-top leading-relaxed whitespace-pre-line">{!! nl2br(e($objective)) !!}</td>
                        <td class="border-2 border-slate-900 p-5 text-slate-800 text-sm font-medium align-top leading-relaxed whitespace-pre-line">{!! nl2br(e($activities)) !!}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        
    @empty
        <div class="text-center py-16 px-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 mt-12">
            <i class="fa fa-clipboard-list text-5xl text-slate-300 mb-4 block"></i>
            <p class="text-base font-bold text-slate-600 italic m-0">No IEP Goals have been assigned to this student yet.</p>
        </div>
    @endforelse

    <table class="mt-16 w-full border-collapse [page-break-inside:avoid]">
        <tbody>
            <tr>
                <td class="w-[40%] text-center align-top">
                    <div class="border-b border-slate-900 h-10 mb-1.5 w-full"></div>
                    <div class="font-black text-sm text-slate-900">{{ $student->teacher->name ?? '_________________________' }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">SPED Teacher</div>
                </td>
                
                <td class="w-[20%]"></td> 
                
                <td class="w-[40%] text-center align-top">
                    <div class="border-b border-slate-900 h-10 mb-1.5 w-full"></div>
                    <div class="font-black text-sm text-slate-900">{{ $student->guardian->name ?? '_________________________' }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">Parent / Guardian</div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center pt-12">
                    <div class="w-[45%] mx-auto block">
                        <div class="border-b border-slate-900 h-10 mb-1.5 w-full"></div>
                        <div class="font-black text-sm text-slate-900">_________________________</div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">School Administrator / SPED Coordinator</div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>