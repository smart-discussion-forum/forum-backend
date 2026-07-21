from flask import Flask, jsonify

app = Flask(__name__)

sample_topics = [
    {"id": 1, "title": "Calculus revision questions", "category": "Mathematics", "posts": 12, "group_activity": 8},
    {"id": 2, "title": "Laravel API patterns", "category": "Programming", "posts": 9, "group_activity": 10},
    {"id": 3, "title": "Physics lab experiments", "category": "Science", "posts": 7, "group_activity": 5},
    {"id": 4, "title": "General discussion board", "category": "General", "posts": 6, "group_activity": 3},
]


@app.get('/recommendations')
def recommendations():
    ranked = sorted(
        sample_topics,
        key=lambda item: (item["group_activity"], item["posts"]),
        reverse=True,
    )

    return jsonify({
        "personalized": [
            {
                "topic_id": item["id"],
                "title": item["title"],
                "category": item["category"],
                "score": round((item["group_activity"] * 0.6) + (item["posts"] * 0.4), 2),
                "reason": "High group activity and recent engagement"
            }
            for item in ranked[:3]
        ],
        "trending": [
            {
                "topic_id": item["id"],
                "title": item["title"],
                "category": item["category"],
                "score": round(item["posts"] * 1.2, 2),
                "reason": "Popular topic this week"
            }
            for item in sorted(sample_topics, key=lambda item: item["posts"], reverse=True)[:3]
        ]
    })


@app.get('/statistics')
def statistics():
    total_topics = len(sample_topics)
    total_posts = sum(item["posts"] for item in sample_topics)
    categories = {}

    for item in sample_topics:
        categories[item["category"]] = categories.get(item["category"], 0) + 1

    return jsonify({
        "summary": {
            "total_topics": total_topics,
            "total_posts": total_posts,
            "top_category": max(categories.items(), key=lambda x: x[1])[0]
        },
        "categories": categories
    })


if __name__ == '__main__':
    app.run(debug=True)
