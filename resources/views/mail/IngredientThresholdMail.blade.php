Hello,
We are emailing you because one ingredient or more has reached its threshold.

The ingredient(s) are:
<x-mail::table>
| id | item | Item Weight | Reaches % | Ordered Weight |
| -- |:----:| -----:| ---:| --------:|
@foreach($ingredients as $ingredient)
    | {{$ingredient['id']}} | {{$ingredient['item']}} | {{$ingredient['weight']}} | {{$ingredient['threshold']}} % | {{$ingredient['weight_sum']}}
@endforeach
</x-mail::table>
