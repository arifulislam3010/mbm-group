<table>
	<thead>
		<tr>
			<th>User Name</th>
			<th>email</th>
			<th>Enrolled course</th>
			<th>Certificate</th>
			<th>Joining date</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $val)
		<tr>
			<td>{{$val->name}}</td>
			<td>{{$val->email}}</td>
			<td>{{$val->total_enrolled_count}}</td>
			<td>{{$val->total_certificate_count}}</td>
			<td>{{$val->joining_date}}</td>
		</tr>
		@endforeach
	</tbody>
</table>