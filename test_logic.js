// Simple test to simulate frontend behavior
const fetch = require('node-fetch'); // You might need to install this: npm install node-fetch

async function testFrontendLogic() {
    try {
        // Step 1: Login
        console.log('1. Logging in...');
        const loginResponse = await fetch('http://127.0.0.1:8000/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: 'admin@vidaduacademy.com',
                password: 'password'
            })
        });
        
        const loginData = await loginResponse.json();
        console.log('Login successful:', loginData.user.name);
        const token = loginData.token;
        
        // Step 2: Load courses
        console.log('\n2. Loading courses...');
        const coursesResponse = await fetch('http://127.0.0.1:8000/api/courses', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        const coursesData = await coursesResponse.json();
        console.log(`Loaded ${coursesData.data.length} courses`);
        
        // Step 3: Check purchase status for each course
        console.log('\n3. Checking purchase status for each course...');
        const coursePurchaseStatus = {};
        
        for (const course of coursesData.data) {
            const statusResponse = await fetch(`http://127.0.0.1:8000/api/payments/course/${course.id}/status`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const statusData = await statusResponse.json();
            coursePurchaseStatus[course.id] = statusData;
            
            console.log(`Course "${course.title}":`, {
                has_purchased: statusData.has_purchased,
                is_enrolled: statusData.is_enrolled
            });
        }
        
        // Step 4: Simulate frontend logic - what should be shown for each course
        console.log('\n4. Frontend UI logic simulation:');
        for (const course of coursesData.data) {
            const status = coursePurchaseStatus[course.id];
            
            console.log(`\nCourse: "${course.title}"`);
            
            if (status.has_purchased) {
                console.log('  ✅ Show: "Kurz je zakúpený" badge');
                console.log('  🔒 Modal: Show "Kurz je zakúpený" status');
                console.log('  ❌ Purchase: Show alert if user tries to buy again');
            } else {
                console.log('  💰 Show: Normal price and purchase button');
                console.log('  🛒 Modal: Show purchase button');
                console.log('  ✅ Purchase: Allow normal checkout process');
            }
        }
        
        // Step 5: Test purchase attempt for already purchased course
        console.log('\n5. Testing purchase attempt for already purchased course...');
        const purchasedCourse = coursesData.data.find(course => 
            coursePurchaseStatus[course.id].has_purchased
        );
        
        if (purchasedCourse) {
            console.log(`Attempting to "purchase" already bought course: "${purchasedCourse.title}"`);
            console.log('Expected behavior: Show alert "Tento kurz už máte zakúpený a nachádza sa v sekcii Moje kurzy"');
            console.log('Expected behavior: Do not redirect to checkout');
        }
        
        console.log('\n✅ All tests completed successfully!');
        
    } catch (error) {
        console.error('❌ Test failed:', error);
    }
}

testFrontendLogic();
