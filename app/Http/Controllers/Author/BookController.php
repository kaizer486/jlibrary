<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    // Define all categories with sub-categories (same as super-admin)
    protected $categories = [
        'Computer Science & Information Technology' => [
            'Programming Languages', 'Web Development', 'Cybersecurity', 'Networking', 
            'Database Management', 'Software Engineering', 'Operating Systems', 'Cloud Computing'
        ],
        'Artificial Intelligence & Data Science' => [
            'Machine Learning', 'Deep Learning', 'Natural Language Processing', 
            'Computer Vision', 'Data Analytics', 'Big Data', 'Robotics'
        ],
        'Engineering & Technology' => [
            'Mechanical Engineering', 'Electrical Engineering', 'Civil Engineering', 
            'Chemical Engineering', 'Aerospace Engineering', 'Biomedical Engineering'
        ],
        'Mathematics & Statistics' => [
            'Algebra', 'Calculus', 'Geometry', 'Probability', 'Statistics', 
            'Applied Mathematics', 'Pure Mathematics'
        ],
        'Physical Sciences' => [
            'Physics', 'Chemistry', 'Astronomy', 'Materials Science', 
            'Geophysics', 'Thermodynamics'
        ],
        'Biological Sciences' => [
            'Biology', 'Genetics', 'Microbiology', 'Biochemistry', 
            'Ecology', 'Evolution', 'Botany', 'Zoology'
        ],
        'Health & Medical Sciences' => [
            'Medicine', 'Nursing', 'Pharmacy', 'Dentistry', 
            'Physiotherapy', 'Medical Research', 'Anatomy', 'Pharmacology'
        ],
        'Public Health' => [
            'Epidemiology', 'Health Policy', 'Nutrition', 'Global Health', 
            'Environmental Health', 'Health Promotion'
        ],
        'Agriculture & Veterinary Sciences' => [
            'Agronomy', 'Animal Science', 'Veterinary Medicine', 'Horticulture', 
            'Soil Science', 'Agricultural Economics'
        ],
        'Environmental & Earth Sciences' => [
            'Ecology', 'Geology', 'Meteorology', 'Oceanography', 
            'Conservation', 'Climate Change', 'Sustainable Development'
        ],
        'Business & Management' => [
            'Management', 'Organizational Behavior', 'Strategic Management', 
            'Human Resources', 'Operations Management'
        ],
        'Economics & Finance' => [
            'Microeconomics', 'Macroeconomics', 'International Economics', 
            'Investments', 'Corporate Finance', 'Financial Markets'
        ],
        'Accounting' => [
            'Financial Accounting', 'Management Accounting', 'Auditing', 
            'Taxation', 'Accounting Information Systems'
        ],
        'Marketing' => [
            'Digital Marketing', 'Consumer Behavior', 'Brand Management', 
            'Advertising', 'Market Research'
        ],
        'Entrepreneurship' => [
            'Startups', 'Business Planning', 'Venture Capital', 'Innovation', 
            'Small Business Management'
        ],
        'Law' => [
            'Constitutional Law', 'Criminal Law', 'Civil Law', 'Corporate Law', 
            'International Law', 'Human Rights Law'
        ],
        'Education' => [
            'Teaching Methods', 'Educational Psychology', 'Curriculum Design', 
            'Higher Education', 'Early Childhood Education'
        ],
        'Social Sciences' => [
            'Sociology', 'Anthropology', 'Social Work', 'Cultural Studies', 
            'Demography', 'Gender Studies'
        ],
        'Psychology' => [
            'Clinical Psychology', 'Cognitive Psychology', 'Developmental Psychology', 
            'Social Psychology', 'Neuropsychology'
        ],
        'Political Science & Public Administration' => [
            'Political Theory', 'International Relations', 'Public Policy', 
            'Public Administration', 'Comparative Politics'
        ],
        'Humanities' => [
            'History', 'Philosophy', 'Linguistics', 'Literature', 
            'Cultural Studies', 'Art History'
        ],
        'Philosophy' => [
            'Ethics', 'Metaphysics', 'Epistemology', 'Logic', 
            'Political Philosophy', 'Aesthetics'
        ],
        'Languages & Linguistics' => [
            'English', 'French', 'Swahili', 'Arabic', 
            'Language Acquisition', 'Translation Studies'
        ],
        'Literature' => [
            'Classical Literature', 'Modern Literature', 'World Literature', 
            'Poetry', 'Drama', 'Literary Criticism'
        ],
        'History & Archaeology' => [
            'World History', 'African History', 'Ancient History', 
            'Archaeology', 'Historical Research'
        ],
        'Geography & Tourism' => [
            'Human Geography', 'Physical Geography', 'Tourism Management', 
            'Cartography', 'Urban Planning'
        ],
        'Religion & Theology' => [
            'Religious Studies', 'Theology', 'Comparative Religion', 
            'Biblical Studies', 'Islamic Studies'
        ],
        'Arts, Design & Music' => [
            'Fine Arts', 'Graphic Design', 'Music Theory', 'Performing Arts', 
            'Fashion Design', 'Digital Arts'
        ],
        'Architecture & Urban Planning' => [
            'Architectural Design', 'Urban Design', 'Landscape Architecture', 
            'Housing Policy', 'Sustainable Cities'
        ],
        'Children\'s Books' => [
            'Picture Books', 'Early Readers', 'Chapter Books', 'Young Adult Fiction', 
            'Children\'s Non-Fiction'
        ],
        'Fiction' => [
            'Literary Fiction', 'Science Fiction', 'Fantasy', 'Mystery', 
            'Romance', 'Historical Fiction', 'Horror'
        ],
        'Non-Fiction' => [
            'True Stories', 'Expository Writing', 'Memoirs', 'Creative Non-Fiction'
        ],
        'Biographies & Memoirs' => [
            'Autobiography', 'Biography', 'Memoir', 'Oral History'
        ],
        'Self-Help & Personal Development' => [
            'Personal Growth', 'Mindfulness', 'Productivity', 'Motivation', 
            'Life Skills', 'Wellness'
        ],
        'Leadership' => [
            'Leadership Theory', 'Organizational Leadership', 'Team Building', 
            'Executive Management', 'Ethical Leadership'
        ],
        'Research & Academic Publications' => [
            'Research Methods', 'Academic Writing', 'Peer Review', 'Research Ethics'
        ],
        'Journals & Conference Proceedings' => [
            'Academic Journals', 'Conference Papers', 'Proceedings', 'Special Issues'
        ],
        'Theses & Dissertations' => [
            'PhD Theses', 'Masters Dissertations', 'Undergraduate Theses', 'Research Projects'
        ],
        'Government Publications' => [
            'Government Reports', 'White Papers', 'Policy Documents', 'Legislation'
        ],
        'Policies, Acts & Regulations' => [
            'National Policies', 'International Agreements', 'Regulatory Frameworks'
        ],
        'Reports & White Papers' => [
            'Technical Reports', 'Business Reports', 'White Papers', 'Policy Briefs'
        ],
        'Reference Books' => [
            'Encyclopedias', 'Dictionaries', 'Handbooks', 'Yearbooks', 'Almanacs'
        ],
        'Open Educational Resources (OER)' => [
            'Open Textbooks', 'OER Collections', 'Open Courseware', 'OER Repositories'
        ],
        'Newspapers & Magazines' => [
            'Daily Newspapers', 'Weekly Magazines', 'Special Interest Publications'
        ],
        'Encyclopedias & Dictionaries' => [
            'General Encyclopedias', 'Subject Encyclopedias', 'Dictionaries', 'Thesauruses'
        ]
    ];

    public function index()
    {
        $books = Book::where('uploaded_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('author.books.index', compact('books'));
    }

    public function create()
    {
        $categories = $this->categories;
        $institutions = Institution::where('status', 'approved')->get();
        
        return view('author.books.create', compact('categories', 'institutions'));
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'is_paid' => 'boolean',
        'institution_id' => 'nullable|exists:institutions,id',
        'book_file' => 'required|file|mimes:pdf|max:20480',
        'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'total_pages' => 'nullable|integer|min:0',
        'category' => 'nullable|string|max:255',
        'sub_category' => 'nullable|string|max:255',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'published_date' => 'nullable|date',
    ]);

    $bookPath = $request->file('book_file')->store('books', 'public');
    
    $coverPath = null;
    if ($request->hasFile('cover_image')) {
        $coverPath = $request->file('cover_image')->store('book-covers', 'public');
    }

    Book::create([
        'title' => $request->title,
        'author' => $request->author,
        'description' => $request->description,
        'price' => $request->price ?? 0,
        'is_paid' => $request->is_paid ?? false,
        'file_path' => $bookPath,
        'cover_image' => $coverPath,
        'total_pages' => $request->total_pages ?? 0,
        'uploaded_by' => auth()->id(),
        'institution_id' => $request->institution_id,
        'status' => 'approved', // ✅ Directly approved, no pending
        'category' => $request->category,
        'sub_category' => $request->sub_category,
        'is_featured' => $request->is_featured ?? false,
        'is_trending' => $request->is_trending ?? false,
        'published_date' => $request->published_date,
    ]);

    return redirect()->route('author.books.index')->with('success', 'Book uploaded successfully and is now live!');
}

    public function show(Book $book)
    {
        // Check if the book belongs to the authenticated user
        if ($book->uploaded_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('author.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        // Check if the book belongs to the authenticated user
        if ($book->uploaded_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = $this->categories;
        $institutions = Institution::where('status', 'approved')->get();
        
        return view('author.books.edit', compact('book', 'categories', 'institutions'));
    }

    public function update(Request $request, Book $book)
    {
        // Check if the book belongs to the authenticated user
        if ($book->uploaded_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_paid' => 'boolean',
            'institution_id' => 'nullable|exists:institutions,id',
            'book_file' => 'nullable|file|mimes:pdf|max:20480',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'total_pages' => 'nullable|integer|min:0',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'published_date' => 'nullable|date',
        ]);

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_paid' => $request->is_paid ?? false,
            'total_pages' => $request->total_pages ?? 0,
            'institution_id' => $request->institution_id,
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'is_featured' => $request->is_featured ?? false,
            'is_trending' => $request->is_trending ?? false,
            'published_date' => $request->published_date,
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->cover_image = $request->file('cover_image')->store('book-covers', 'public');
            $book->save();
        }

        if ($request->hasFile('book_file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $book->file_path = $request->file('book_file')->store('books', 'public');
            $book->save();
        }

        return redirect()->route('author.books.index')->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        // Check if the book belongs to the authenticated user
        if ($book->uploaded_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }
        
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $book->delete();
        
        return redirect()->route('author.books.index')->with('success', 'Book deleted successfully!');
    }
}