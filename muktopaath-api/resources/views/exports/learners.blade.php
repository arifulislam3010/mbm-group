<table>
	<thead>
		<tr>
			<th>Course Name</th>
			<th>Start date</th>
			<th>completion date</th>
			<th>status</th>
			<th>progress</th>
			<th>Grade</th>
			<th>Enrolled by</th>

		</tr>
	</thead>
	<tbody>
		@foreach($data as $val)
		<tr>
			<td>{{$val->course_alias_name}}</td>
			<td>{{$val->start_date}}</td>
			<td>--</td>
			<td></td>
			<td>{{$val->progress_count}}/{{$val->lessons_count}} lessons</td>
			<td></td>
			<td>
				@if($val->enrolled_by_admin==1)
				Enrolled by admin
				@else
				Self Enrolled
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>