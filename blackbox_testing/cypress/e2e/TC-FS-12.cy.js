describe("TC-FS-12", () => {
    it("TC-FS-12", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Set expiration date").click();

        const futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 366);
        const formattedDate = futureDate.toISOString().split('T')[0];

        cy.get('input[type="date"]').type(formattedDate);

        cy.contains("button", "Create").click();

        cy.contains("Expiration date cannot be more than").should("be.visible");
    });
});