<?php
function session_form_values(array $input) {
    return ['title'=>trim($input['title']??''),'date'=>trim($input['date']??''),'session_type'=>trim($input['session_type']??'Erg'),'workout_format'=>trim($input['workout_format']??'single_distance'),'status'=>trim($input['status']??'upcoming'),'purpose'=>trim($input['purpose']??''),'boat_class'=>trim($input['boat_class']??''),'coach_notes'=>trim($input['coach_notes']??'')];
}
function session_segments(array $input) {
    $segments=[];
    foreach (($input['segments']??[]) as $s) { if (!is_array($s)) continue; $segments[]=[
        'segment_type'=>trim($s['segment_type']??''),'title'=>trim($s['title']??''),'repetitions'=>trim($s['repetitions']??''),'work_value'=>trim($s['work_value']??''),'work_unit'=>trim($s['work_unit']??''),'recovery_value'=>trim($s['recovery_value']??''),'recovery_unit'=>trim($s['recovery_unit']??''),'target_rate'=>trim($s['target_rate']??''),'target_split'=>trim($s['target_split']??''),'notes'=>trim($s['notes']??'')]; }
    return $segments;
}
function validate_session_form(array $data,array $segments) {
    $errors=[];
    if ($data['title']===''||strlen($data['title'])>255) $errors[]='Enter a session title of 255 characters or fewer.';
    $date=DateTime::createFromFormat('Y-m-d',$data['date']); if(!$date||$date->format('Y-m-d')!==$data['date']) $errors[]='Choose a valid training date.';
    if(!in_array($data['session_type'],['Erg','Water'],true)) $errors[]='Choose Erg or Water as the session type.';
    if(!in_array($data['status'],['upcoming','open','completed'],true)) $errors[]='Choose a valid session status.';
    if($data['purpose']!==''&&!in_array($data['purpose'],['UT2','UT1','AT','TR','Race/Test','Technique'],true)) $errors[]='Choose a valid workout purpose.';
    if($data['boat_class']!==''&&!in_array($data['boat_class'],['8+','4+','4-','2-','2x','1x','Other'],true)) $errors[]='Choose a valid boat class.';
    if($data['session_type']==='Erg'&&!in_array($data['workout_format'],['single_distance','single_time','distance_intervals','time_intervals','variable'],true)) $errors[]='Choose a valid erg workout format.';
    if(strlen($data['coach_notes'])>5000) $errors[]='Coach notes must be 5,000 characters or fewer.';
    if(!$segments) $errors[]=$data['session_type']==='Water'?'Add at least one shed or piece.':'Add the workout details.';
    $types=['single_distance','single_time','distance_interval','time_interval','variable_distance','variable_time','water_shed']; $units=['metres','seconds','minutes','strokes'];
    foreach($segments as $i=>$s){$label=$data['session_type']==='Water'?'Shed '.($i+1):'Workout segment '.($i+1);
        if(!in_array($s['segment_type'],$types,true))$errors[]="$label has an invalid type.";
        if($s['work_unit']!==''&&!in_array($s['work_unit'],$units,true))$errors[]="$label has an invalid work unit.";
        if($s['recovery_unit']!==''&&!in_array($s['recovery_unit'],$units,true))$errors[]="$label has an invalid recovery unit.";
        if($s['repetitions']!==''&&(!ctype_digit($s['repetitions'])||(int)$s['repetitions']<1||(int)$s['repetitions']>999))$errors[]="$label repetitions must be between 1 and 999.";
        if($data['session_type']==='Water'&&$s['title']==='')$errors[]="$label needs a description.";
        if($data['session_type']==='Erg'&&$s['work_value']==='')$errors[]="$label needs a distance or duration.";
        foreach(['title'=>255,'work_value'=>100,'recovery_value'=>100,'target_rate'=>50,'target_split'=>50,'notes'=>5000] as $f=>$max)if(strlen($s[$f])>$max)$errors[]="$label contains a field that is too long.";
    } return array_values(array_unique($errors));
}
function nullable_session_value($v){return $v===''?null:$v;}
function save_segments(PDO $pdo,int $session_id,array $segments){$q=$pdo->prepare('INSERT INTO workout_segments (session_id,segment_order,segment_type,title,repetitions,work_value,work_unit,recovery_value,recovery_unit,target_rate,target_split,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');foreach($segments as $i=>$s)$q->execute([$session_id,$i+1,$s['segment_type'],nullable_session_value($s['title']),nullable_session_value($s['repetitions']),nullable_session_value($s['work_value']),nullable_session_value($s['work_unit']),nullable_session_value($s['recovery_value']),nullable_session_value($s['recovery_unit']),nullable_session_value($s['target_rate']),nullable_session_value($s['target_split']),nullable_session_value($s['notes'])]);}
