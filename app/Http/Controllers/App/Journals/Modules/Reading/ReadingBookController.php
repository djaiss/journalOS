<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Modules\Reading;

use App\Actions\CreateBook;
use App\Actions\LogBook;
use App\Actions\RemoveBook;
use App\Enums\BookStatus;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ReadingBookController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');

        $validated = $request->validate([
            'book_name' => ['required', 'string', 'max:255'],
        ]);

        $bookName = TextSanitizer::plainText($validated['book_name']);
        $book = $this->findOrCreateBook(Auth::user(), $bookName);

        $alreadyLogged = $entry->books()->where('books.id', $book->id)->exists();
        if (! $alreadyLogged) {
            new LogBook(
                user: Auth::user(),
                entry: $entry,
                book: $book,
                status: BookStatus::CONTINUED,
            )->execute();
        }

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $entry = $request->attributes->get('journal_entry');
        $bookId = (int) $request->route('book');

        $book = Book::query()
            ->where('user_id', Auth::id())
            ->findOrFail($bookId);

        new RemoveBook(
            user: Auth::user(),
            entry: $entry,
            book: $book,
        )->execute();

        return to_route('journal.entry.show', [
            'slug' => $entry->journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
        ])->with('status', __('Changes saved'));
    }

    private function findOrCreateBook(User $user, string $bookName): Book
    {
        $existingBook = Book::query()
            ->where('user_id', $user->id)
            ->get()
            ->first(fn(Book $book) => $book->name === $bookName);

        if ($existingBook !== null) {
            return $existingBook;
        }

        return new CreateBook(
            user: $user,
            name: $bookName,
        )->execute();
    }
}
