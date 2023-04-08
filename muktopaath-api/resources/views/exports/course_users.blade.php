<table>
	<thead>
		<tr>
			<th>User Name</th>
			<th>email</th>
			<th>Joining date</th>
			<th>Progress</th>
			<th>Completion date</th>
			<th>Grade</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $val)
		<tr>
			<td>{{$val->name}}</td>
			<td>{{$val->email}}</td>
			<td>{{$val->joining_date}}</td>
			<td>{{$val->course_progress_count}}</td>
			<td>--</td>
			<td></td>
		</tr>
		@endforeach
	</tbody>
</table>