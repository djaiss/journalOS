<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Journals\Modules\Reading;

use App\Actions\CreateBook;
use App\Actions\LogBook;
use App\Actions\RemoveBook;
use App\Enums\BookStatus;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ReadingBookController extends Controller
{
    public function store(Request $request): JsonResponse
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

        $entry->load('moduleReading', 'books');

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
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

        $entry->load('moduleReading', 'books');

        return response()->json([
            'data' => new JournalEntryResource($entry),
        ], 200);
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
