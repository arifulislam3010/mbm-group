<table>
	<thead>
		<tr>
			<th>Course Name</th>
			<th>Rating</th>
			<th>Batch Name</th>
			<th>Status</th>
			<th>Enrollments</th>
			<th>passed</th>
			<th>Payment</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data as $val)
		<tr>
			<td>{{$val->course_alias_name}}</td>
			<td>{{$val->rating}}</td>
			<td>{{$val->title}}</td>
			<td>
				@if($val->courses_for_status==1)
				private
				@else
				public
				@endif
			</td>
			<td>{{$val->total_enrollment}}</td>
			<td>{{$val->passed_count}}</td>
			<td></td>
		</tr>
		@endforeach
	</tbody>
</table>