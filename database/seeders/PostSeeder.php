<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{

    public function run(): void
    {
         $noerine = User::where('email', 'noerine@mindshare.com')->first();
        $jonathan = User::where('email', 'jonathan@mindshare.com')->first();
        $joel = User::where('email', 'joel@mindshare.com')->first();
        $lecturer = User::where('email', 'lecturer@mindshare.com')->first();

        $topic1 = Topic::find(1); // Introduction to Software Engineering
        $topic2 = Topic::find(2); // Agile vs Waterfall
        $topic3 = Topic::find(3); // Getting Started with Laravel
        $topic4 = Topic::find(4); // RESTful APIs
        $topic5 = Topic::find(5); // Entity Relationship Diagrams
        $topic6 = Topic::find(6); // SQL vs NoSQL

        //Posts for topic 1
       Post::create([
            'topic_id' => $topic1->id,
            'user_id' => $noerine->id,
            'content' => 'Software engineering is the systematic application of engineering approaches to software development. It covers design, development, testing, and maintenance.',
        ]); 

        Post::create([
            'topic_id' => $topic1->id,
            'user_id' => $jonathan->id,
            'content' => 'I think the most important aspect is requirements gathering. If you get that wrong, everything else falls apart.',
        ]);

        Post::create([
            'topic_id' => $topic1->id,
            'user_id' => $joel->id,
            'content' => 'Agreed. The SDD we wrote for MindShare is a good example of proper requirements documentation before building.',
        ]);

              Post::create([
            'topic_id' => $topic2->id,
            'user_id' => $jonathan->id,
            'content' => 'Agile is better for projects where requirements change frequently. Waterfall works when everything is fixed upfront.',
        ]);

        Post::create([
            'topic_id' => $topic2->id,
            'user_id' => $lecturer->id,
            'content' => 'Both have their place. The key is understanding your project context before choosing a methodology.',
        ]);

        Post::create([
            'topic_id' => $topic3->id,
            'user_id' => $lecturer->id,
            'content' => 'Laravel follows the Model View Controller pattern. Start by understanding how routes, controllers, and views connect to each other.',
        ]);

        Post::create([
            'topic_id' => $topic3->id,
            'user_id' => $noerine->id,
            'content' => 'Migrations are one of the best features in Laravel. They make database management across a team much cleaner.',
        ]);

         Post::create([
            'topic_id' => $topic3->id,
            'user_id' => $joel->id,
            'content' => 'Eloquent relationships are also very powerful once you understand hasMany, belongsTo, and belongsToMany.',
        ]);

        Post::create([
            'topic_id' => $topic4->id,
            'user_id' => $joel->id,
            'content' => 'A RESTful API uses HTTP methods: GET to retrieve data, POST to create, PUT or PATCH to update, and DELETE to remove.',
        ]);

        Post::create([
            'topic_id' => $topic4->id,
            'user_id' => $jonathan->id,
            'content' => 'Sanctum is great for securing Laravel APIs. It handles token-based authentication cleanly.',
        ]);

         Post::create([
            'topic_id' => $topic5->id,
            'user_id' => $lecturer->id,
            'content' => 'An Entity Relationship Diagram shows entities, their attributes, and the relationships between them using cardinality notation.',
        ]);

        Post::create([
            'topic_id' => $topic5->id,
            'user_id' => $noerine->id,
            'content' => 'We used a drawio Entity Relationship Diagram for MindShare and it helped a lot when writing the data dictionary.',
        ]);

        // Posts for topic 6
        Post::create([
            'topic_id' => $topic6->id,
            'user_id' => $noerine->id,
            'content' => 'SQL databases are relational and use structured schemas. NoSQL databases are more flexible but sacrifice some consistency guarantees.',
        ]);

               Post::create([
            'topic_id' => $topic6->id,
            'user_id' => $joel->id,
            'content' => 'For MindShare we are using MySQL which is relational — the right choice given how interconnected our entities are.',
        ]);
 
    }
}
